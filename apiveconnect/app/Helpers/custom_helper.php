<?php

use Config\Services;
use Mpdf\Mpdf;
use App\Models\ExhibitorContactPersonModel;

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
        $session = session();
        $sub_event_id = $session->get('sub_event_id');
        
        if (empty($sub_event_id)) {
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
        $builder->where('m.sub_event_id', $sub_event_id);
        $builder->orderBy('m.id', 'ASC');
        $builder->orderBy('p.serial_no', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        $menu = [];
        
        foreach ($result as $row) {
            if (!isset($menu[$row['menu_id']])) {
                $menu[$row['menu_id']] = [
                    'menu_id'   => $row['menu_id'],
                    'menu_name' => $row['menu_name'],
                    'pages'     => []
                ];
            }
            
            if (!empty($row['page_id'])) {
                $menu[$row['menu_id']]['pages'][] = [
                    'page_id'    => $row['page_id'],
                    'page_title' => $row['page_title'],
                    'page_url'   => $row['page_url'],
                    'serial_no'  => $row['serial_no']
                ];
            }
        }
        
        return $menu;
    }
}

function send_sms_remote($mobile, $otp, $portal)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://securenationexpo.com/SmsGateway/sendOtp',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'mobile' => $mobile,
            'otp'    => $otp,
            'portal' => $portal,
        ],
        CURLOPT_SSL_VERIFYPEER => ENVIRONMENT !== 'production',
        CURLOPT_SSL_VERIFYHOST => ENVIRONMENT !== 'production' ? 0 : 2,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    log_message('info', "[send_sms_remote] Response: {$response}, HTTP Code: {$httpCode}, Error: {$error}");
    curl_close($ch);
    if ($error) {
        log_message('error', "[send_sms_remote] cURL error: {$error}");
        return false;
    }
    
    if ($httpCode !== 200) {
        log_message('error', "[send_sms_remote] HTTP {$httpCode} for mobile={$mobile}");
        return false;
    }
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        log_message('error', '[send_sms_remote] Invalid JSON: ' . $response);
        return false;
    }
    
    return (bool) ($result['status'] ?? false);
}

if (!function_exists('send_sms_otp')) {
    function send_sms_otp($mobile, $otp)
    {
        $params = [
            'user'        => '20090418',
            'pwd'         => 'Globe@2020',
            'senderid'    => 'SIEVNT',
            'CountryCode' => '91',
            'mobileno'    => preg_replace('/\D/', '', $mobile),
            'msgtext'     => "Your Exhibitor Login OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International",
            'pe_id'       => '1701159229231639515',
            'template_id' => '1777178634395350287',
            'smstype'     => '0'
        ];

        $url = "http://www.mshastra.com/sendurl.aspx?" . http_build_query($params);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        log_message('info', "[send_sms_otp] Response: {$response}");
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorNo  = curl_errno($ch);

        curl_close($ch);

        if ($errorNo != 0) {
            log_message('error', "[send_sms_otp] cURL error number: {$errorNo}");
            return false;
        }

        if ($httpCode == 200 && !empty($response)) {
            log_message('info', "[send_sms_otp] SMS sent successfully to {$mobile}");
            return true;
        }

        log_message('error', "[send_sms_otp] Failed to send SMS to {$mobile}. HTTP Code: {$httpCode}. Response: {$response}");
        return false;
    }
}

if (!function_exists('resolvePortalBranding')) {
    function resolvePortalBranding(string $referralWebsite): array
    {
        $defaultEmail = env('DEFAULT_FROM_EMAIL', 'akannaujiya@servintonline.com');
        
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
        string $fromName  = '',
        array  $attachments = []
    ): bool {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            log_message('error', "[sendEmail] Invalid recipient: {$toEmail}");
            return false;
        }
        
        $fromEmail = $fromEmail ?: env('DEFAULT_FROM_EMAIL', 'info@bridalasia.com');
        $fromName  = $fromName  ?: env('DEFAULT_FROM_NAME',  'Exhibitor Portal');
        
        $payload = [
            'from'             => ['email' => $fromEmail, 'name' => $fromName],
            'subject'          => $subject,
            'content'          => [['type' => 'html', 'value' => $htmlBody]],
            'personalizations' => [[
                'to' => [['email' => $toEmail, 'name' => $toName]],
            ]],
        ];
        
        if (!empty($attachments)) {
            $payloadAttachments = [];
            
            foreach ($attachments as $filePath) {
                if (!file_exists($filePath)) {
                    log_message('error', "[sendEmail] Attachment file not found: {$filePath}");
                    continue;
                }
                
                $fileContent = file_get_contents($filePath);
                
                if ($fileContent === false) {
                    log_message('error', "[sendEmail] Failed to read attachment: {$filePath}");
                    continue;
                }
                
                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
                
                $payloadAttachments[] = [
                    'content' => base64_encode($fileContent),
                    'type'    => $mimeType,
                    'name'    => basename($filePath),
                ];
            }
            
            if (!empty($payloadAttachments)) {
                $payload['attachments'] = $payloadAttachments;
            }
        }

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
        string $referralWebsite = '',
        ?int $subEventId = null
    ): bool {
        $branding = resolvePortalBranding($referralWebsite);
        
        $isInternational = false;
        
        if (isset($user->exhibitor_type) && !empty($user->exhibitor_type)) {
            $internationalCodes = ['International'];
            
            if (!in_array($user->exhibitor_type, $internationalCodes)) {
                $isInternational = true;
            }
        }
        
        $mobile = $user->mobile ?? $user->mobile_number ?? '';
        $email = $user->email ?? '';
        
        $results = [];
        
        $otpMessage = "Your Exhibitor Login OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International";
        
        if ($channel === 'email' || $channel === 'both') {
            if (!empty($email)) {
                $form = null;
                
                if ($subEventId) {
                    $formModel = new ExhibitorContactPersonModel();
                    $form = $formModel->getSubEvents($subEventId);
                }
                
                $uploadBaseUrl = env('UPLOAD_BASE_URL');
                $viewPath = APPPATH . 'Views/' . str_replace('/', DIRECTORY_SEPARATOR, $branding['otpView']) . '.php';
                
                $viewData = [
                    'message'              => $otpMessage,
                    'sub_event_name'       => $form->sub_event_name ?? $branding['portalName'],
                    'logo'                 => $form ? $uploadBaseUrl . $form->sub_event_logo : $branding['logoUrl'],
                    'sub_event_date_image' => $form ? $uploadBaseUrl . $form->sub_event_date_image : '',
                ];

                $htmlBody = '';
                
                if (is_file($viewPath)) {
                    $htmlBody = view($branding['otpView'], $viewData);
                } else {
                    $htmlBody = '<p>' . htmlspecialchars($otpMessage, ENT_QUOTES, 'UTF-8') . '</p>';
                }

                $emailResult = sendEmail(
                    toEmail: $email,
                    toName: $user->first_name ?? 'User',
                    subject: $branding['portalName'] . ' — Login OTP',
                    htmlBody: $htmlBody,
                    fromEmail: $branding['fromEmail'],
                    fromName: $branding['fromName']
                );
                
                $results['email'] = $emailResult;
            }
        }
        
        if ($channel === 'mobile' || $channel === 'both') {
            if (!$isInternational && !empty($mobile)) {
                $mobileResult = send_sms_otp($mobile, $otp);
                $results['mobile'] = $mobileResult;
            } else if ($isInternational) {
                log_message('info', "[sendOtpMessage] International user - SMS skipped for mobile: {$mobile}");
                $results['mobile'] = false;
            }
        }
        
        if ($channel === 'email' && !empty($email)) {
            return $results['email'] ?? false;
        }
        
        if ($channel === 'mobile' && !empty($mobile) && !$isInternational) {
            return $results['mobile'] ?? false;
        }
        
        if ($channel === 'both') {
            if ($isInternational) {
                return $results['email'] ?? false;
            }
            
            return ($results['email'] ?? false) || ($results['mobile'] ?? false);
        }
        
        return false;
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
                    return [
                        'name' => basename($attachment), 
                        'content' => base64_encode(file_get_contents($attachment))
                    ];
                }
                
                if (is_array($attachment) && !empty($attachment['name']) && isset($attachment['content'])) {
                    return [
                        'name' => $attachment['name'], 
                        'content' => base64_encode((string)$attachment['content'])
                    ];
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

if (!function_exists('getAuthContext')) {
    function getAuthContext(): ?array
    {
        $request = service('request');
        $token = $request->getCookie('api_token');

        if (empty($token)) {
            log_message('error', 'getAuthContext: api_token cookie not found');
            return null;
        }

        $decrypted = decryptData($token);
        
        if ($decrypted === false || $decrypted === null) {
            log_message('error', 'getAuthContext: api_token decryption failed');
            return null;
        }

        $payload = json_decode($decrypted, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            log_message('error', 'getAuthContext: api_token payload is not valid JSON');
            return null;
        }

        $vendorId   = $payload['vendorId'] ?? $payload['exhibitor_id'] ?? null;
        $subEventId = $payload['subEventId'] ?? $payload['sub_event_id'] ?? null;

        if (empty($vendorId) || empty($subEventId)) {
            log_message('error', 'getAuthContext: vendorId or subEventId missing from token payload');
            return null;
        }

        return [
            'vendorId'   => (int) $vendorId,
            'subEventId' => (int) $subEventId,
        ];
    }
}

if (!function_exists('formatMobileWithCountryCode')) {
    function formatMobileWithCountryCode(?string $country_code, ?string $mobile_number)
    {
        $mobile_number = ltrim(trim((string) $mobile_number), '0');
        
        if (empty($mobile_number) || empty($country_code)) {
            return $mobile_number;
        }

        return '+' . ltrim(trim($country_code), '+') . ' ' . $mobile_number;
    }
}

if (!function_exists('normalizeTableBorders')) {
    function normalizeTableBorders(string $html): string
    {
        if (stripos($html, '<table') === false) {
            return $html;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?><div id="__root__">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        
        libxml_clear_errors();

        $changed = false;

        $extractBorderColor = function (string $style): ?string {
            if (preg_match('/(?<!-)border-color\s*:\s*([^;]+)/i', $style, $m)) {
                return trim($m[1]);
            }
            return null;
        };

        $hasFullBorder = function (string $style): bool {
            if (preg_match('/(?<!-)border\s*:/i', $style)) {
                return true;
            }
            
            $hasStyle = (bool) preg_match('/border-style\s*:/i', $style);
            $hasWidth = (bool) preg_match('/border-width\s*:/i', $style);
            
            return $hasStyle && $hasWidth;
        };

        foreach (iterator_to_array($dom->getElementsByTagName('table')) as $table) {
            $hasBorderAttr = $table->hasAttribute('border') && $table->getAttribute('border') !== '0';
            $style = $table->getAttribute('style');

            if (!$hasBorderAttr || $hasFullBorder($style)) {
                continue;
            }

            $color = $extractBorderColor($style) ?? '#000';

            if (stripos($style, 'border-collapse') === false) {
                $style = rtrim(trim($style), '; ') . '; border-collapse: collapse;';
                $table->setAttribute('style', trim($style, '; '));
                $changed = true;
            }

            foreach (['td', 'th'] as $tag) {
                foreach (iterator_to_array($table->getElementsByTagName($tag)) as $cell) {
                    $cellStyle = $cell->getAttribute('style');
                    
                    if ($hasFullBorder($cellStyle)) {
                        continue;
                    }
                    
                    $cellColor = $extractBorderColor($cellStyle) ?? $color;
                    $cellStyle = rtrim(trim($cellStyle), '; ') . "; border: 1px solid {$cellColor};";
                    $cell->setAttribute('style', trim($cellStyle, '; '));
                    $changed = true;
                }
            }
        }

        if (!$changed) {
            return $html;
        }

        $root = $dom->getElementById('__root__');
        $out = '';
        
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        
        return $out;
    }
}

if (!function_exists('getInternationalStatus')) {
    function getInternationalStatus($user): bool
    {
        if (isset($user->exhibitor_type) && !empty($user->exhibitor_type)) {
            $internationalCodes = ['international'];
            
            if (!in_array($user->exhibitor_type, $internationalCodes)) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('sendOtpViaEmail')) {
    function sendOtpViaEmail($user, $otp, $referralWebsite, $subEventId = null): bool
    {
        $branding = resolvePortalBranding($referralWebsite);
        
        $form = null;
        
        if ($subEventId) {
            $formModel = new ExhibitorContactPersonModel();
            $form = $formModel->getSubEvents($subEventId);
        }
        
        $otpMessage = "Your Exhibitor Login OTP Code is {$otp}. This code is valid for 15 minutes. Exhibition Managed by Services International";
        
        $uploadBaseUrl = env('UPLOAD_BASE_URL');
        $viewPath = APPPATH . 'Views/' . str_replace('/', DIRECTORY_SEPARATOR, $branding['otpView']) . '.php';
        
        $viewData = [
            'message'              => $otpMessage,
            'sub_event_name'       => $form->sub_event_name ?? $branding['portalName'],
            'logo'                 => $form ? $uploadBaseUrl . $form->sub_event_logo : $branding['logoUrl'],
            'sub_event_date_image' => $form ? $uploadBaseUrl . $form->sub_event_date_image : '',
        ];

        $htmlBody = '';
        
        if (is_file($viewPath)) {
            $htmlBody = view($branding['otpView'], $viewData);
        } else {
            $htmlBody = '<p>' . htmlspecialchars($otpMessage, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        return sendEmail(
            toEmail: $user->email,
            toName: $user->first_name ?? 'User',
            subject: $branding['portalName'] . ' — Login OTP',
            htmlBody: $htmlBody,
            fromEmail: $branding['fromEmail'],
            fromName: $branding['fromName']
        );
    }
}

if (!function_exists('sendOtpViaMobile')) {
    function sendOtpViaMobile($user, $otp): bool
    {
        $mobile = $user->mobile ?? $user->mobile_number ?? '';
        
        if (empty($mobile)) {
            return false;
        }
        
        return send_sms_otp($mobile, $otp);
    }
}