<?php

namespace App\Controllers;

use App\Models\ExhibitorModel;

class AuthController extends BaseController
{
    protected $session;
    protected $exhibitorModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->exhibitorModel = new ExhibitorModel();
        helper('custom');
    }

    public function generate_main_event_code($enc_id = null)
    {
        $encrypted_id = encryptData(4);
        return redirect()->to('login/' . $encrypted_id);
    }

    public function index($enc_id = null)
    {

        $referreral_website = $this->request->getServer('HTTP_REFERER');
        $decrypted_id = decryptData($enc_id);
        if (!$decrypted_id) {
            return redirect()->to('event/' . $enc_id)->with('fail', 'Invalid request');
        }
        $subevents = $this->exhibitorModel->getActiveSubEvents($decrypted_id);
        if (empty($subevents)) {
            return redirect()->to('event/' . $enc_id)->with('fail', 'No active events found');
        }
        $this->session->set([
            'enc_sub_event_id' => $enc_id,
            'referreral_website' => $referreral_website,
        ]);
        if (count($subevents) === 1) {
            $encrypted_sub_event_id = encryptData($subevents[0]->sub_event_id);
            return redirect()->to('login/' . $encrypted_sub_event_id);
        }
        return view('event', ['subevents' => $subevents]);
    }

    public function exlogin($encrypted_sub_event_id = null)
    {
        if (!$encrypted_sub_event_id) {
            return $this->generate_main_event_code();
        }

        $sub_event_id = decryptData($encrypted_sub_event_id);
        if (!$sub_event_id) {
            return redirect()->to('event/' . $encrypted_sub_event_id)->with('fail', 'Invalid event data');
        }

        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }

        return view('login', [
            'enc_sub_event_id' => $encrypted_sub_event_id,
            'referreral_website' => $this->session->get('referreral_website'),
        ]);
    }

    public function sendOtp()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request format.'
            ]);
        }
        $identifier = trim($this->request->getPost('identifier'));
        $enc_sub_event_id = $this->request->getPost('enc_sub_event_id');
        $sub_event_id = decryptData($enc_sub_event_id);
        if (!$sub_event_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid event data.'
            ]);
        }
        if ($identifier === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please enter your email or mobile number.'
            ]);
        }
        $isEmail  = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;
        $isMobile = preg_match('/^\+?[0-9\s-]{7,20}$/', $identifier) === 1;
        if (!$isEmail && !$isMobile) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enter a valid email address or mobile number.'
            ]);
        }
        $user = $this->exhibitorModel
            ->findContactPersonByIdentifierAndSubEvent(
                $identifier,
                (int) $sub_event_id
            );
        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No account matches this email or mobile number for this event.'
            ]);
        }
        $otp = str_pad((string) random_int(111111, 999999), 6, '0', STR_PAD_LEFT);
        $channel = $isEmail ? 'email' : 'mobile';
        $referralWebsite = $this->session->get('referreral_website') ?? '';
        $this->session->set([
            'otp_user_id'       => $user->id,
            'otp_exhibitor_id'  => $user->exhibitor_id,
            'otp_sub_event_id'  => $sub_event_id,
            'referreral_website' => $referralWebsite,
            'otp_code'          => $otp,
            'otp_channel'       => $channel,
            'otp_expire_at'     => time() + 300,
            'otp_identifier'    => $identifier,
        ]);
        $emailSent = sendOtpMessage(
            $user,
            $otp,
            $channel,
            $referralWebsite
        );
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $emailSent
                ? 'OTP sent to your registered ' . ($channel === 'email' ? 'email address' : 'mobile number') . '.'
                : 'OTP generated successfully.',
            'channel' => $channel,
            'debug_otp' => (ENVIRONMENT === 'development') ? $otp : null,
        ]);
    }

    public function verifyOtp()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request format.']);
        }
        $otp = trim($this->request->getPost('otp'));
        if (!preg_match('/^\d{6}$/', $otp)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Enter the 6-digit OTP that was sent to you.']);
        }
        $storedOtp = $this->session->get('otp_code');
        $expiresAt = (int) $this->session->get('otp_expire_at');
        $userId = $this->session->get('otp_user_id');
        if (!$storedOtp || !$userId || $expiresAt < time()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'OTP expired or missing. Please request a new one.']);
        }
        if (!hash_equals((string) $storedOtp, (string) $otp)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid OTP. Please try again.']);
        }
        $user = $this->exhibitorModel->findContactPersonById($userId);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User account could not be found.']);
        }
        $this->session->remove(['login_attempts', 'lock_until', 'lock_duration', 'otp_code', 'otp_expire_at', 'otp_user_id', 'otp_exhibitor_id', 'otp_channel', 'otp_identifier']);
        $this->session->regenerate(true);
        $this->session->set([
            'user_id' => $user->id,
            'user_name' => $user->first_name ?? '',
            'user_email' => $user->email,
            'exhibitor_id' => $user->exhibitor_id,
            'referreral_website' => $this->session->get('referreral_website'),
            'sub_event_id' => $this->session->get('otp_sub_event_id') ?? $this->session->get('sub_event_id'),
            'logged_in' => true,
        ]);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'OTP verified. Login successful.',
            'redirect' => base_url('dashboard'),
        ]);
    }

    public function checkLogin()
    {
        $lock_until = $this->session->get('lock_until');
        if ($lock_until && time() < $lock_until) {
            $remaining = ceil(($lock_until - time()) / 60);
            return redirect()->back()
                ->with('fail', "Too many attempts. Try again after {$remaining} minutes");
        }
        $rules = ['email' => 'required|valid_email', 'password' => 'required|min_length[5]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('email_error', $this->validator->getError('email'))
                ->with('password_error', $this->validator->getError('password'));
        }
        $email = strtolower(trim($this->request->getPost('email')));
        $password = $this->request->getPost('password');
        $enc_sub_event_id = decryptData($this->request->getPost('enc_sub_event_id'));
        if (!$enc_sub_event_id) {
            return redirect()->back()->with('fail', 'Invalid Event Data');
        }
        $user = $this->exhibitorModel->getContactPersonByEmailAndSubEvent($email, $enc_sub_event_id);
        if (!$user || !password_verify($password, $user->password)) {
            $attempts = $this->session->get('login_attempts') ?? 0;
            $attempts++;
            $this->session->set('login_attempts', $attempts);
            if ($attempts >= 3) {
                $previous_lock = $this->session->get('lock_duration') ?? 15;
                $this->session->set([
                    'lock_until' => time() + ($previous_lock * 60),
                    'lock_duration' => $previous_lock,
                    'login_attempts' => 0,
                ]);
                return redirect()->back()
                    ->with('fail', "Too many failed attempts. Locked for {$previous_lock} minutes");
            }
            return redirect()->back()->withInput()
                ->with('fail', 'Invalid email or password');
        }
        $this->session->remove(['login_attempts', 'lock_until', 'lock_duration']);
        $this->session->regenerate(true);
        $this->session->set([
            'user_id' => $user->id,
            'user_name' => $user->first_name ?? '',
            'user_email' => $user->email,
            'exhibitor_id' => $user->exhibitor_id,
            'sub_event_id' => $enc_sub_event_id,

            'logged_in' => true,
        ]);
        return redirect()->to(base_url('dashboard'))
            ->with('success', 'Login successful');
    }

    public function logout()
    {
        $referreral_website = $this->session->get('referreral_website');
        $this->session->destroy();
        return redirect()->to($referreral_website);
    }

    public function error404()
    {
        $enc_id = $this->session->get('enc_sub_event_id');
        return response()->setStatusCode(404)->setBody(
            view('errors/custom_404', [
                'enc_id' => $enc_id,
            ])
        );
    }
}
