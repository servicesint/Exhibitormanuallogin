<?php

use Config\Services;
use Mpdf\Mpdf;
use App\Models\ExhibitorModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generate_pdf')) {
    function generate_pdf(string $view, string $filename = 'file.pdf', array $data = [], $download = true)
    {
        $mpdf = new Mpdf();
        $html = view($view, $data);
        
        $mpdf->WriteHTML($html);
        $disposition = $download ? 'attachment' : 'inline';
        return service('response')
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->setBody($mpdf->Output('', 'S'));
    }
}

if (!function_exists('getManualSidebar')) {
    function getManualSidebar()
    {
        $request = service('request');
        $token = $request->getCookie('api_token');
        if (empty($token)) {

            log_message(
                'error',
                'getManualSidebar: api_token cookie not found'
            );

            return [];
        }

        try {
            $jwtSecret = env('JWT_SECRET');
            if (empty($jwtSecret)) {
                log_message(
                    'error',
                    'getManualSidebar: JWT_SECRET missing'
                );
                return [];
            }
            $decoded = JWT::decode(
                $token,
                new Key($jwtSecret, 'HS256')
            );

        } catch (\Throwable $e) {
            log_message(
                'error',
                'JWT Decode Error: ' . $e->getMessage()
            );
            return [];
        }
        $sub_event_id =
            $payload['sub_event_id']
            ?? $payload['subEventId']
            ?? null;
        if (empty($sub_event_id)) {
            log_message(
                'error',
                'getManualSidebar: sub_event_id missing from token'
            );
            return [];
        }
        $db = \Config\Database::connect();
        $builder = $db->table('manual_pages_menu m');
        $builder->select("
            m.id as menu_id,
            m.menu_name,
            p.id as page_id,
            p.page_title,
            p.page_url,
            p.serial_no
        ");
        $builder->join(
            'manual_pages p',
            'p.menu_id = m.id AND p.is_deleted = 0',
            'left'
        );
        $builder->where('m.is_deleted', 0);
        $builder->where(
            'm.sub_event_id',
            $sub_event_id
        );
        $builder->orderBy(
            'm.id',
            'ASC'
        );
        $builder->orderBy(
            'p.serial_no',
            'ASC'
        );
        $result = $builder
            ->get()
            ->getResultArray();
        /*
        |--------------------------------------------------------------------------
        | CREATE SIDEBAR ARRAY
        |--------------------------------------------------------------------------
        */

        $menu = [];
        foreach ($result as $row) {
            if (!isset($menu[$row['menu_id']])) {
                $menu[$row['menu_id']] = [
                    'menu_id' => $row['menu_id'],
                    'menu_name' => $row['menu_name'],
                    'pages' => []
                ];
            }
            if (!empty($row['page_id'])) {
                $menu[$row['menu_id']]['pages'][] = [
                    'page_id' => $row['page_id'],
                    'page_title' => $row['page_title'],
                    'page_url' => $row['page_url'],
                    'serial_no' => $row['serial_no']
                ];
            }
        }
        return $menu;
    }
}

// if (!function_exists('send_sms')) {
//     function send_sms($mobile_number, $otp, string $referralWebsite = ''): bool
//     {

//         try {
//             $mobile_number = preg_replace('/\D/', '', $mobile_number);
//             if (strlen($mobile_number) == 12 && substr($mobile_number, 0, 2) == '91') {
//                 $mobile_number = substr($mobile_number, 2);
//             }
//             if (!preg_match('/^[6-9][0-9]{9}$/', $mobile_number)) {
//                 log_message('error', 'Invalid mobile number: ' . $mobile_number);
//                 return false;
//             }
//             if ($referralWebsite == 'drone') {
//                 $msg = "Your Drone Expo registration OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International.";
//             } else if ($referralWebsite == 'bridalasia') {
//                 $msg = "Your Bridal Asia registration OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International.";
//             } else {
//                 $msg = "Your Drone Expo registration OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International.";
//             }

//             $url = "";

//             $postData = [
//                 'mobile'  => $mobile_number,
//                 'message' => $msg,
//             ];

//             $ch = curl_init();
//             curl_setopt_array($ch, [
//                 CURLOPT_URL            => $url,
//                 CURLOPT_RETURNTRANSFER => true,
//                 CURLOPT_POST           => true,
//                 CURLOPT_POSTFIELDS     => http_build_query($postData),
//                 CURLOPT_TIMEOUT        => 30,
//                 CURLOPT_SSL_VERIFYPEER => false,
//             ]);
//             $response = curl_exec($ch);
//             $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//             $curlError = curl_error($ch);
//             curl_close($ch);
//             if ($curlError) {
//                 log_message('error', 'SMS CURL Error: ' . $curlError);
//                 return false;
//             }
//             if ($httpCode == 200) {
//                 log_message('info', 'OTP sent successfully to ' . $mobile_number);
//                 return true;
//             }
//             log_message('error', 'SMS API Response: ' . $response);
//             return false;
//         } catch (\Throwable $e) {
//             log_message('error', 'send_sms Exception: ' . $e->getMessage());
//             return false;
//         }
//     }
// }

function send_sms_remote($mobile, $otp, $portal)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://securenationexpo.com/SmsGateway/sendOtp',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'mobile' => $mobile,
            'otp'    => $otp,
            'portal' => $portal,
        ],
    ]);
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);
    if ($error) {
        log_message('error', $error);
        return false;
    }
    $result = json_decode($response, true);
    return $result['status'] ?? false;
}


if (!function_exists('resolvePortalBranding')) {
    function resolvePortalBranding(string $referralWebsite): array
    {
        $defaultEmail = env('DEFAULT_FROM_EMAIL', 'noreply@example.com');
        if (stripos($referralWebsite, 'bridalasia') !== false) {
            return [
                'portalName' => 'Bridal Asia',
                'fromEmail'  => env('BRIDALASIA_FROM_EMAIL', $defaultEmail),
                'fromName'   => env('BRIDALASIA_FROM_NAME',  'Bridal Asia'),
                'logoUrl'    => env('BRIDALASIA_LOGO_URL',   ''),
                'otpView'    => 'email/otp_bridal_asia',
            ];
        } else if (stripos($referralWebsite, 'drone') !== false) {
            return [
                'portalName' => 'Drone Expo',
                'fromEmail'  => env('DRONEEXPO_FROM_EMAIL', $defaultEmail),
                'fromName'   => env('DRONEEXPO_FROM_NAME',  'Drone Expo'),
                'logoUrl'    => env('DRONEEXPO_LOGO_URL',   ''),
                'otpView'    => 'email/otp_drone_expo',
            ];
        } else if (stripos($referralWebsite, 'fireindia') !== false) {
            return [
                'portalName' => 'Fire India',
                'fromEmail'  => env('FIREINDIA_FROM_EMAIL', $defaultEmail),
                'fromName'   => env('FIREINDIA_FROM_NAME',  'Fire India'),
                'logoUrl'    => env('FIREINDIA_LOGO_URL',   ''),
                'otpView'    => 'email/otp_fire_india',
            ];
        } else if (stripos($referralWebsite, 'Securenation') !== false) {
            return [
                'portalName' => 'Securenation',
                'fromEmail'  => env('SECURENATION_FROM_EMAIL', $defaultEmail),
                'fromName'   => env('SECURENATION_FROM_NAME',  'Securenation'),
                'logoUrl'    => env('SECURENATION_LOGO_URL',   ''),
                'otpView'    => 'email/otp_securenation',
            ];
        }
        return [
            'portalName' => 'Exhibitor Portal',
            'fromEmail'  => $defaultEmail,
            'fromName'   => env('DEFAULT_FROM_NAME', 'Exhibitor Portal'),
            'logoUrl'    => env('DEFAULT_LOGO_URL',  ''),
            'otpView'    => 'email/otp_default',
        ];
    }
}
if (!function_exists('sendEmail')) {
    function sendEmail(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $fromEmail = '',
        string $fromName  = ''
    ): bool {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            log_message('error', "[sendEmail] Invalid recipient: {$toEmail}");
            return false;
        }
        $fromEmail = $fromEmail ?: env('DEFAULT_FROM_EMAIL', 'noreply@example.com');
        $fromName  = $fromName  ?: env('DEFAULT_FROM_NAME',  'Exhibitor Portal');
        $payload = [
            'from'             => ['email' => $fromEmail, 'name' => $fromName],
            'subject'          => $subject,
            'content'          => [['type' => 'html', 'value' => $htmlBody]],
            'personalizations' => [[
                'to' => [['email' => $toEmail, 'name' => $toName]],
            ]],
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://emailapi.netcorecloud.net/v5/mail/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'api_key: '    . env('NETCORE_API_KEY'),
                'content-type: application/json',
            ],
        ]);
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);
        if ($err) {
            log_message('error', "[sendEmail] cURL error: {$err}");
            return false;
        }
        $res = json_decode($response);

        if (!isset($res->status) || $res->status !== 'success') {
            log_message('error', "[sendEmail] Netcore error response: {$response}");
            return false;
        }
        return true;
    }
}

if (!function_exists('sendOtpMessage')) {
    function sendOtpMessage(
        $user,
        string $otp,
        string $channel,
        string $referralWebsite = ''
    ): bool {
        $session = session();
        $branding = resolvePortalBranding($referralWebsite);
        if ($channel === 'email') {
            if (empty($user->email)) {
                return false;
            }
            $formModel = new ExhibitorModel();
            $form = $formModel->getSubEvents($session->get('otp_sub_event_id'));
            if (stripos($referralWebsite, 'drone') !== false) {
                $msg = "Your Drone Expo Login OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International.";
            } elseif (stripos($referralWebsite, 'bridalasia') !== false) {
                $msg = "Your Bridal Asia Login OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International.";
            } elseif (stripos($referralWebsite, 'fireindia') !== false) {
                $msg = "Your Fire India Login OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International.";
            } elseif (stripos($referralWebsite, 'securenation') !== false) {
                $msg = "Your Securenation Login OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International.";
            } else {
                $msg = "Your OTP Code is {$otp}. This code is valid for 15 minutes.";
            }
            $uploadBaseUrl = env('UPLOAD_BASE_URL');
            $htmlBody = view($branding['otpView'], [
                'message'              => $msg,
                'sub_event_name'       => $form->sub_event_name,
                'logo'                 => $uploadBaseUrl . $form->sub_event_logo,
                'sub_event_date_image' => $uploadBaseUrl . $form->sub_event_date_image,
            ]);
            return sendEmail(
                toEmail: $user->email,
                toName: '',
                subject: $branding['portalName'] . ' — Login OTP',
                htmlBody: $htmlBody,
                fromEmail: $branding['fromEmail'],
                fromName: $branding['fromName']
            );
        } else {
            if (empty($user->mobile_number)) {
                return false;
            }
            return send_sms_remote(
                $user->mobile_number,
                $otp,
                $referralWebsite
            );
        }
        return true;
    }
}

if (!function_exists('pr')) {
    function pr($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;
    }
}

if (!function_exists('send_common_mail')) {
    function send_common_mail($to, string $subject, string $msg, array $options = []): array
    {
        $apiKey = $options['api_key'] ?? env('EMAIL_API_KEY');
        if (!$apiKey) {
            return ['success' => false, 'message' => 'Email API key is not configured.'];
        }
        $normalize = function ($items) {
            if (empty($items)) {
                return [];
            }
            if (is_string($items)) {
                $items = [[$items, '']];
            }
            return array_values(array_map(function ($item) {
                if (is_string($item)) {
                    return ['email' => $item, 'name' => ''];
                }
                if (is_array($item)) {
                    return [
                        'email' => $item['email'] ?? ($item[0] ?? ''),
                        'name' => $item['name'] ?? ($item[1] ?? ''),
                    ];
                }
                return ['email' => '', 'name' => ''];
            }, $items));
        };
        $to = $normalize($to);
        $personalization = ['to' => $to];
        foreach (['cc', 'bcc'] as $field) {
            if (!empty($options[$field])) {
                $personalization[$field] = $normalize($options[$field]);
            }
        }
        if (!empty($options['reply_to'])) {
            $personalization['reply_to'] = $normalize($options['reply_to'])[0] ?? ['email' => '', 'name' => ''];
        }
        $payload = [
            'from' => [
                'email' => $options['from']['email'] ?? env('EMAIL_FROM', 'info@bridalasia.com'),
                'name' => $options['from']['name'] ?? env('EMAIL_FROM_NAME', 'Bridal Asia'),
            ],
            'subject' => $subject,
            'content' => [['type' => 'html', 'value' => $msg]],
            'personalizations' => [$personalization],
        ];
        if (!empty($options['attachments'])) {
            $payload['attachments'] = array_values(array_filter(array_map(function ($attachment) {
                if (is_string($attachment) && file_exists($attachment)) {
                    return ['name' => basename($attachment), 'content' => base64_encode(file_get_contents($attachment))];
                }
                if (is_array($attachment) && !empty($attachment['name']) && isset($attachment['content'])) {
                    return ['name' => $attachment['name'], 'content' => base64_encode((string)$attachment['content'])];
                }
                return null;
            }, $options['attachments'])));
        }
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://emailapi.netcorecloud.net/v5/mail/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'api_key: ' . $apiKey,
                'content-type: application/json',
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return ['success' => false, 'message' => $err, 'payload' => $payload];
        }
        $decoded = json_decode($response, true);
        return ['success' => isset($decoded['status']) && $decoded['status'] === 'success', 'response' => $decoded];
    }
}
