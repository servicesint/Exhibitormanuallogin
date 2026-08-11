<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ExhibitorContactPersonModel;
use App\Services\JwtService;
use App\Libraries\JwtPayload;

class AuthController extends BaseController
{
    protected $contactModel;
    protected $jwtService;

    public function __construct()
    {
        $this->contactModel = new ExhibitorContactPersonModel();
        $this->jwtService = new JwtService();
    }

    public function sendOtp()
    {
        $identifier = trim((string) $this->request->getVar('identifier'));
        $enc_sub_event_id = $this->request->getVar('enc_sub_event_id');
        $subEventId = $this->getSubEventIdFromRequest($enc_sub_event_id);
        if (!$identifier) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please enter your email or mobile number.'
            ]);
        }
        if (!$subEventId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid event data.']);
        }
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;
        $isMobile = preg_match('/^\+?[0-9\s-]{7,20}$/', $identifier) === 1;
        if (!$isEmail && !$isMobile) {
            return $this->response->setJSON(['status' => false, 'message' => 'Enter a valid email address or mobile number.']);
        }

        $user = $this->contactModel->findContactPersonByIdentifierAndSubEvent(
            $identifier,
            $subEventId
        );

        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No account matches this email or mobile number for this event.'
            ]);
        }
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->contactModel->updateRecord(
            $user->id,
            [
                'otp' => $otp,
                'otp_expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
                'otp_resend_count' => 0,
                'otp_last_sent_at' => date('Y-m-d H:i:s'),
                'otp_verified' => 0
            ]
        );
        $channel = $isEmail ? 'email' : 'mobile';
        $referralWebsite = (string) ($this->request->getVar('referreral_website') ?? $this->request->getVar('referral_website') ?? '');
        $otpSent = sendOtpMessage($user, $otp, $channel, $referralWebsite, $subEventId);
        if (!$otpSent) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'OTP could not be sent'
            ]);
        }
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'OTP sent successfully.',
            'channel' => $channel,
            'debug_otp' => (ENVIRONMENT === 'development') ? $otp : null,
        ]);
    }

    public function verifyOtp()
    {
        $identifier = trim((string) $this->request->getVar('identifier'));
        $otp = trim((string) $this->request->getVar('otp'));
        $subEventId = $this->getSubEventIdFromRequest();
        if (!$identifier || !$otp) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Identifier and OTP are required'
            ]);
        }
        if (!$subEventId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid sub event'
            ]);
        }
        $user = $this->contactModel->findContactPersonByIdentifierAndSubEvent(
            $identifier,
            $subEventId
        );
        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'User not found'
            ]);
        }
        if (empty($user->otp)) {
            if ($user->otp !== '') {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ]);
            }
            if ($user->otp !== $otp) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ]);
            }
            $expiresAt = empty($user->otp_expire_at) ? false : strtotime($user->otp_expire_at);
            if (!$expiresAt || $expiresAt < time()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'OTP expired'
                ]);
            }
            $token = $this->jwtService->generateToken(['uid' => $user->id, 'exhibitor_id' => $user->exhibitor_id, 'email' => $user->email]);
            $this->contactModel->update(
                $user->id,
                [
                    'otp_verified' => 1,
                    'otp' => null
                ]
            );
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'OTP verified.',
                'token'   => generateJwt($user, $subEventId),
                'expires_in' => 86400,
            ]);
        }
    }

    public function resendOtp()
    {
        $identifier = trim((string) $this->request->getVar('identifier'));
        $subEventId = $this->getSubEventIdFromRequest();
        if (!$identifier) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Identifier is required.'
            ]);
        }
        if (!$subEventId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid sub event'
            ]);
        }
        $user = $this->contactModel->findContactPersonByIdentifierAndSubEvent(
            $identifier,
            $subEventId
        );
        if (!$user) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'User not found.'
            ]);
        }
        $resendCount = (int) ($user->otp_resend_count ?? 0);
        $waitTime = ($resendCount + 1) * 15;
        $lastSentAt = strtotime($user->otp_last_sent_at ?? '1970-01-01');
        $allowedAt = $lastSentAt + $waitTime;
        if (time() < $allowedAt) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please wait before requesting another OTP.',
                'remaining_seconds' => $allowedAt - time()
            ]);
        }
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $updateData = [
            'otp'               => $otp,
            'otp_expire_at'     => date(
                'Y-m-d H:i:s',
                strtotime('+5 minutes')
            ),
            'otp_last_sent_at'  => date('Y-m-d H:i:s'),
            'otp_resend_count'  => $resendCount + 1,
            'otp_verified'      => 0
        ];
        $this->contactModel->update($user->id, $updateData);
        $channel = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $referralWebsite = (string) (
            $this->request->getVar('referreral_website')
            ?? $this->request->getVar('referral_website')
            ?? ''
        );
        $otpSent = sendOtpMessage($user, $otp, $channel, $referralWebsite, $subEventId);
        if (!$otpSent) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'OTP could not be sent'
            ]);
        }
        return $this->response->setJSON([
            'status' => true,
            'message' => 'OTP resent successfully.',
            'next_resend_after' => ($resendCount + 2) * 15,
            'expires_in' => 300,
            'debug_otp' => ENVIRONMENT === 'development'
                ? $otp
                : null
        ]);
    }

    private function getSubEventIdFromRequest($enc_sub_event_id = null): ?int
    {
        if ($enc_sub_event_id === null) {
            $enc_sub_event_id = $this->request->getVar('enc_sub_event_id');
        }
        $subEventId = decryptData($enc_sub_event_id);
        if ($subEventId === false || !ctype_digit((string) $subEventId)) {
            return null;
        }
        $subEventId = (int) $subEventId;
        return $subEventId > 0 ? $subEventId : null;
    }

    public function logout()
    {
        $payload = JwtPayload::get();
        $userId  = $payload->sub ?? null;
        if (!$userId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null
                ]);
        }

        $this->contactModel->updateRecord($userId, [
            'otp'          => null,
            'otp_verified' => 0,
            'otp_expire_at' => null,
        ]);

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Logged out successfully.',
                'data'    => null
            ]);
    }

    public function get_sub_events($encryptedEventId = null)
    {
        try {
            $db = \Config\Database::connect();
            if (!$encryptedEventId) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Event reference is required.'
                    ]);
            }
            $encryptedEventId = urldecode($encryptedEventId);
            $eventId = decryptData($encryptedEventId);
            if (!$eventId || !is_numeric($eventId)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Invalid event reference.'
                    ]);
            }
            $eventId = (int) $eventId;
            $event = $db->table('company_events')
                ->select('id, event_name, url')
                ->where('id', $eventId)
                ->where('is_deleted', 0)
                ->get()
                ->getRowArray();
            if (!$event) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Event not found.'
                    ]);
            }
            $subEvents = $db->table('company_sub_events')
                ->select('id, sub_event_name, sub_event_logo')
                ->where('event_id', $eventId)
                ->where('is_deleted', 0)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
            $uploadBaseUrl = rtrim(env('UPLOAD_BASE_URL', ''), '/');
            $data = [];
            foreach ($subEvents as $row) {
                $logoUrl = '';
                if (!empty($row['sub_event_logo'])) {
                    $logoUrl = filter_var($row['sub_event_logo'], FILTER_VALIDATE_URL)
                        ? $row['sub_event_logo']
                        : $uploadBaseUrl . '/' . ltrim($row['sub_event_logo'], '/');
                } else {
                    $logoUrl = base_url('assets/images/new-default.jpg');
                }
                $data[] = [
                    'sub_event_id'   => encryptData($row['id']),
                    'sub_event_name' => $row['sub_event_name'],
                    'sub_event_logo' => $logoUrl,
                ];
            }
            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'message' => 'Sub events fetched successfully.',
                'data'    => [
                    'event_name' => $event['event_name'],
                    'event_url' => $event['url'],
                    'sub_events' => $data
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'get_sub_events failed: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'success' => false,
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine()
                ]);
        }
    }
}
