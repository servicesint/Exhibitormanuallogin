<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ExhibitorModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class DashboardController extends BaseController
{
    protected $db;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }
    public function index(): string
    {
        $model = new ExhibitorModel();
        $data['profile'] = $model->getProfile(session()->get('exhibitor_id'));
        return view('exhibitor/profile', $data);
    }

    public function exhibitor_dashboard()
    {
        $token = $_COOKIE['api_token'] ?? '';

        try {
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                return redirect()->to('login');
            }

            $payload = json_decode(
                base64_decode(
                    str_pad(
                        strtr($parts[1], '-_', '+/'),
                        strlen($parts[1]) % 4,
                        '=',
                        STR_PAD_RIGHT
                    )
                ),
                true
            );

            if (!$payload || empty($payload['sub_event_id'])) {
                return redirect()->to('login');
            }
        } catch (\Exception $e) {
            log_message('error', '[dashboard] Token decode failed: ' . $e->getMessage());
            return redirect()->to('login');
        }

        $subEventId = (int) $payload['sub_event_id'];

        $db = \Config\Database::connect();

        $sql = "
            SELECT
                ms.manual_welcome_note,
                c.company_name,
                c.company_logo,
                cse.sub_event_name,
                cse.venue,
                cse.venue_city,
                cse.full_date,
                cse.start_date,
                cse.end_date,
                ce.event_name,
                e.organisation_name,
                e.stall_number
            FROM manual_setups AS ms
            LEFT JOIN company_sub_events AS cse
                ON cse.id = ms.sub_event_id
            LEFT JOIN company_events AS ce
                ON ce.id = cse.event_id
            LEFT JOIN companies AS c
                ON c.id = cse.company_id
            LEFT JOIN exhibitors AS e
                ON e.sub_event_id = cse.id
            WHERE ms.sub_event_id = ?
            LIMIT 1
        ";

        $template = $db->query($sql, [$subEventId])->getRowArray();

        if (!$template) {
            return redirect()->to('login');
        }

        $fullDate = $template['full_date'] ?? '';

        if (empty($fullDate) && !empty($template['start_date'])) {
            if (
                !empty($template['end_date']) &&
                $template['start_date'] != $template['end_date']
            ) {
                $start = new \DateTime($template['start_date']);
                $end = new \DateTime($template['end_date']);

                if ($start->format('F Y') === $end->format('F Y')) {
                    $fullDate = strtoupper($start->format('jS')) . ' - ' .
                        strtoupper($end->format('jS')) . ' ' .
                        $end->format('F Y');
                } else {
                    $fullDate = strtoupper($start->format('jS')) . ' ' .
                        $start->format('F') . ' - ' .
                        strtoupper($end->format('jS')) . ' ' .
                        $end->format('F Y');
                }
            } else {
                $fullDate = date(
                    'jS F Y',
                    strtotime($template['start_date'])
                );
            }
        }

        $uploadBaseUrl = rtrim(
            env('UPLOAD_BASE_URL', getenv('UPLOAD_BASE_URL')),
            '/'
        );

        $companyLogo = !empty($template['company_logo'])
            ? $uploadBaseUrl . '/' . ltrim($template['company_logo'], '/')
            : '';

        $placeholders = [
            '{{company_name}}' => $template['company_name'] ?? '',
            '{{company_logo}}' => $companyLogo,
            '{{event_name}}' => $template['event_name'] ?? '',
            '{{sub_event_name}}' => $template['sub_event_name'] ?? '',
            '{{venue}}' => $template['venue'] ?? '',
            '{{venue_city}}' => $template['venue_city'] ?? '',
            '{{full_date}}' => $fullDate,
            '{{exhibitor_company}}' => $template['organisation_name'] ?? '',
            '{{stall_number}}' => $template['stall_number'] ?? '',
            '{{date}}' => date('jS M Y'),
        ];

        $welcomeNote = str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $template['manual_welcome_note'] ?? ''
        );

        return view('dashboard', [
            'welcome_note' => $welcomeNote,
            'sub_event_id' => $subEventId,
            'token' => $token,
        ]);
    }

    public function profileFragment()
    {
        $session = session();
        $exhibitorId = $session->get('exhibitor_id');
        if (!$exhibitorId) {
            return $this->response->setStatusCode(401)->setBody('Unauthorized');
        }
        $model = new ExhibitorModel();
        $profile = $model->getProfile($exhibitorId);
        return view('exhibitor/_profile_fragment', ['profile' => $profile]);
    }

    public function save()
    {
        $method = $this->request->getMethod(true);
        if ($method !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed: ' . $method]);
        }
        $session = session();
        $exhibitorId = $session->get('exhibitor_id');
        if (!$exhibitorId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[255]',
            'brand_name'   => 'permit_empty|max_length[255]',
            'email'        => 'permit_empty|valid_email|max_length[255]',
            'phone_number' => 'permit_empty|numeric|min_length[10]|max_length[12]',
            'mobile_number' => 'permit_empty|numeric|min_length[10]|max_length[12]',
            'stand_number' => 'permit_empty|alpha_numeric_space|max_length[50]',
            'area'         => 'permit_empty|numeric|greater_than[0]',
            'contact_person' => 'permit_empty|alpha_space|max_length[255]',
            'address'      => 'permit_empty|max_length[500]',
            'profile_text' => 'permit_empty|max_length[5000]',
            'company_product_specialization' => 'permit_empty|max_length[5000]',
            'company_profile' => 'permit_empty|max_length[5000]',
            'contact_person_name' => 'permit_empty|alpha_space|max_length[255]',
            'contact_person_number' => 'permit_empty|numeric|min_length[10]|max_length[12]',
            'contact_person_email' => 'permit_empty|valid_email|max_length[255]',
        ];
        $messages = [
            'company_name' => [
                'required' => 'Company name is required',
                'min_length' => 'Company name must be at least 2 characters',
                'max_length' => 'Company name cannot exceed 255 characters',
            ],
            'email' => [
                'valid_email' => 'Please enter a valid email address',
                'max_length' => 'Email cannot exceed 255 characters',
            ],
            'phone_number' => [
                'numeric' => 'Phone number must contain only digits',
                'min_length' => 'Phone number must be at least 10 digits',
                'max_length' => 'Phone number cannot exceed 12 digits',
            ],
            'mobile_number' => [
                'numeric' => 'Mobile number must contain only digits',
                'min_length' => 'Mobile number must be at least 10 digits',
                'max_length' => 'Mobile number cannot exceed 12 digits',
            ],
            'stand_number' => [
                'alpha_numeric_space' => 'Stand number can only contain letters, numbers, and spaces',
                'max_length' => 'Stand number cannot exceed 50 characters',
            ],
            'area' => [
                'numeric' => 'Area must be a number',
                'greater_than' => 'Area must be greater than 0',
            ],
            'contact_person' => [
                'alpha_space' => 'Contact person name can only contain letters and spaces',
                'max_length' => 'Contact person name cannot exceed 255 characters',
            ],
            'contact_person_number' => [
                'numeric' => 'Contact person number must contain only digits',
                'min_length' => 'Contact person number must be at least 10 digits',
                'max_length' => 'Contact person number cannot exceed 12 digits',
            ],
            'contact_person_email' => [
                'valid_email' => 'Please enter a valid email for contact person',
                'max_length' => 'Email cannot exceed 255 characters',
            ],
        ];
        if (! $this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }
        $post = $this->request->getPost();
        $data = [
            'organisation_name' => trim($post['company_name'] ?? ''),
            'brand_name' => trim($post['brand_name'] ?? '') ?: null,
            'contact_email' => trim($post['email'] ?? '') ?: null,
            'organisation_address' => trim($post['address'] ?? '') ?: null,
            'contact_person' => trim($post['contact_person'] ?? '') ?: null,
            'contact_number' => trim($post['mobile_number'] ?? '') ?: null,
            'landline' => trim($post['phone_number'] ?? '') ?: null,
            'stall_number' => trim($post['stand_number'] ?? '') ?: null,
            'stall_size' => ((int) ($post['area'] ?? 0) > 0) ? (int) $post['area'] : null,
            'brand_profile' => trim($post['company_profile'] ?? '') ?: null,
            'company_product_specialization' => trim($post['company_product_specialization'] ?? '') ?: null,
            'company_profile' => trim($post['company_profile'] ?? '') ?: null,
            'contact_person_name' => trim($post['contact_person_name'] ?? '') ?: null,
            'contact_person_number' => trim($post['contact_person_number'] ?? '') ?: null,
            'contact_person_email' => trim($post['contact_person_email'] ?? '') ?: null,
        ];
        $fileFields = [
            'brand_logo' => 'brand_logo',
            'app_logo' => 'app_logo',
        ];
        foreach ($fileFields as $field => $column) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);
                $data[$column] = base_url('writable/uploads/' . $newName);
            }
        }
        $model = new ExhibitorModel();
        try {
            $saved = $model->saveProfile($exhibitorId, $data);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to save profile',
            ]);
        }
        if (! $saved) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to save profile',
            ]);
        }
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile saved successfully',
            'data' => [
                'organisation_name' => $data['organisation_name'],
                'brand_name' => $data['brand_name'],
                'contact_email' => $data['contact_email'],
            ],
        ]);
    }

    public function uploadInfo()
    {
        $session = session();
        $isAjax = $this->request->isAJAX();
        $exhibitorId = $session->get('enc_sub_event_id');
        if (!$exhibitorId) {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized request.']);
            }
            $session->setFlashdata('fail', 'Unauthorized request.');
            return redirect()->to('/action');
        }
        $post = $this->request->getPost();
        $allow = ['stand_wording', 'stand_layout', 'invitation_tickets', 'add_tickets', 'shell_space', 'profile', 'chief_executive', 'specialization', 'products_display', 'electricity_load', 'raw_design', 'raw_design_status', 'fabricator_details'];
        foreach ($post as $field => $val) {
            if (!in_array($field, $allow, true)) {
                if ($isAjax) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request, Please try again.']);
                }
                $session->setFlashdata('fail', 'Invalid Request, Please try again.');
                return redirect()->to('/action');
            }
        }
        $file = $this->request->getFile('raw_design');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);
            $post['raw_design'] = base_url('writable/uploads/' . $newName);
            $post['raw_design_status'] = '1';
        }
        if (isset($post['shell_space']) && $post['shell_space'] === 'Shell Space') {
            $post['raw_design'] = '';
            $post['raw_design_status'] = '0';
            $post['fabricator_details'] = '';
            $post['electricity_load'] = '';
        } elseif (isset($post['shell_space']) && $post['shell_space'] === 'Raw Space') {
            $post['stand_wording'] = '';
            $post['stand_layout'] = '';
        }
        // $this->db->table('exlogs')->insert([
        //     'exid' => $exhibitorId,
        //     'exname' => $session->get('user_name') ?? '',
        //     'comment' => 'Updated Details : ' . implode("\n", $post),
        // ]);
        $updated = $this->db->table('exhibitors')
            ->where('id', $exhibitorId)
            ->update($post);
        $redirectUrl = '/action';
        if ($updated) {
            if ($isAjax) {
                return $this->response->setJSON(['success' => true, 'message' => 'Your information submitted successfully.', 'redirect' => $redirectUrl]);
            }
            $session->setFlashdata('success', 'Your information submitted successfully.');
            return redirect()->to($redirectUrl);
        }
        if ($isAjax) {
            return $this->response->setJSON(['success' => false, 'message' => 'Something went wrong, Please try again.', 'redirect' => $redirectUrl]);
        }
        $session->setFlashdata('fail', 'Something went wrong, Please try again.');
        return redirect()->to($redirectUrl);
    }
    public function generateParticipationLetter()
    {
        return generate_pdf('pdf/participation-letter', 'participation.pdf');
    }
    // public function generateExitPermit()
    // {
    //     return generate_pdf('pdf/exit-permit', 'exit-permit.pdf');
    // }

    public function generateExitPermit()
    {
        $token = $_COOKIE['api_token'] ?? '';

        if (empty($token)) {
            die('Token not found');
        }

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            die('Invalid token');
        }

        $payload = json_decode(base64_decode(str_pad(
            strtr($parts[1], '-_', '+/'),
            strlen($parts[1]) % 4,
            '=',
            STR_PAD_RIGHT
        )), true);
        $exhibitorId = $payload['sub'] ?? 0;
        $data['exitPermitData'] = $this->db->table('exhibitors e')
            ->select('
                e.stall_number,
                e.organisation_name
            ')
            ->join('exhibitor_contact_persons ecp', 'ecp.exhibitor_id = e.id', 'left')
            ->where('ecp.id', $exhibitorId)
            ->get()
            ->getRowArray();

        return generate_pdf(
            'pdf/exit-permit',
            'exit-permit.pdf',
            $data
        );
    }
    public function casual_gst(): string
    {
        return view('exhibitor/casual-get-deatils');
    }
    public function important_information(): string
    {
        $model = new ExhibitorModel();
        $data['important'] =  $model->getById('manual_pages', ['menu_id' => 7], 'page_content');
        return view('exhibitor/important-information', $data);
    }
    public function accommodation(): string
    {
        $model = new ExhibitorModel();
        $data['accommodation'] =  $model->getById('manual_pages', ['menu_id' => 9], 'page_content');
        return view('exhibitor/accommadation', $data);
    }
    public function empanelled_sand(): string
    {
        $model = new ExhibitorModel();
        $data['empanelled_sand'] =  $model->getById('manual_pages', ['menu_id' => 11], 'page_content');
        return view('exhibitor/empanelled-sand', $data);
    }
    public function contact_details(): string
    {
        $model = new ExhibitorModel();
        $data['contact'] =  $model->getById('manual_pages', ['menu_id' => 5], 'page_content');
        return view('exhibitor/contact-details', $data);
    }
    public function other_information(): string
    {
        $model = new ExhibitorModel();
        $data['other_information'] =  $model->getById('manual_pages', ['menu_id' => 10], 'page_content');
        return view('exhibitor/other-informations', $data);
    }
    public function raw_space(): string
    {
        return view('exhibitor/raw-space');
    }
    public function casual_gst_get(): string
    {
        return view('exhibitor/casual-gst');
    }
    public function exhibitor_badge(): string
    {
        return view('exhibitor/exhibitor-badge');
    }

    public function submit_exhibitor_badge()
    {
        if ($this->request->getMethod(true) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }
        $rules = [
            'fname' => 'required|min_length[2]|max_length[50]',
            'lname' => 'permit_empty|max_length[50]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'mobile' => 'permit_empty|numeric|min_length[10]|max_length[15]',
            'photo' => [
                'uploaded[photo]',
                'max_size[photo,1024]',
                'ext_in[photo,jpg,jpeg,png]',
                'mime_in[photo,image/jpeg,image/png]',
            ],
        ];
        $messages = [
            'fname' => [
                'required' => 'First name is required.',
                'min_length' => 'First name must have at least 2 characters.',
                'max_length' => 'First name cannot exceed 50 characters.',
            ],
            'lname' => [
                'max_length' => 'Last name cannot exceed 50 characters.',
            ],
            'email' => [
                'valid_email' => 'Please enter a valid email address.',
                'max_length' => 'Email cannot exceed 255 characters.',
            ],
            'mobile' => [
                'numeric' => 'Whatsapp number must contain digits only.',
                'min_length' => 'Whatsapp number must be at least 10 digits.',
                'max_length' => 'Whatsapp number cannot exceed 15 digits.',
            ],
            'photo' => [
                'uploaded' => 'Please upload a photo for the badge.',
                'max_size' => 'Photo must be smaller than 1 MB.',
                'ext_in' => 'Photo must be JPG or PNG.',
                'mime_in' => 'Photo must be a valid image file.',
            ],
        ];
        if (! $this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $errors, 'message' => reset($errors)]);
        }
        $post = $this->request->getPost();
        if (empty($post['email']) && empty($post['mobile'])) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Please provide either an email or Whatsapp number.']);
        }
        $photo = $this->request->getFile('photo');
        if (! $photo->isValid() || $photo->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Uploaded photo is invalid.']);
        }
        $imageData = getimagesize($photo->getTempName());
        if ($imageData === false) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Uploaded file is not a valid image.']);
        }
        $width = $imageData[0] ?? 0;
        if ($width < 300 || $width > 1000) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Photo width must be between 300px and 1000px.']);
        }
        $uploadPath = FCPATH . 'uploads/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $newName = uniqid('badge_', true) . '.' . $photo->getClientExtension();
        $photo->move($uploadPath, $newName);
        $post['photo'] = base_url('uploads/' . $newName);
        $post['uncode'] = bin2hex(random_bytes(16));
        $session = session();
        $exhibitorId = $session->get('exhibitor_id');
        if (! $exhibitorId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $post['exid'] = $exhibitorId;
        $row = $this->db->table('exhibitors')->where('id', $post['exid'])->get()->getRowArray();
        $post['event_name'] = $row['id'] ?? '';
        $saveData = [
            'fname' => $post['fname'],
            'lname' => $post['lname'] ?? null,
            'email' => $post['email'] ?? null,
            'mobile' => $post['mobile'] ?? null,
            'photo' => $post['photo'],
            'uncode' => $post['uncode'],
            'exid' => $post['exid'] ?? null,
            'event_name' => $post['event_name'],
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $inserted = $this->db->table('exbadges')->insert($saveData);
        if (! $inserted) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Unable to save badge.']);
        }

        if (! empty($post['email']) && function_exists('send_common_mail')) {
            $photoFile = $uploadPath . $newName;
            $photoMime = $photo->getClientMimeType();
            $photoBase64 = base64_encode(file_get_contents($photoFile));
            $badgeHtml = '<div style="font-family: Arial, sans-serif; color: #333;">'
                . '<h1 style="text-align:center;">Exhibitor Badge</h1>'
                . '<table style="width:100%; border-collapse: collapse;">'
                . '<tr><td style="padding:8px; border:1px solid #ddd; width:35%;"><strong>Name</strong></td><td style="padding:8px; border:1px solid #ddd;">' . htmlspecialchars($post['fname'] . ' ' . ($post['lname'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td></tr>'
                . '<tr><td style="padding:8px; border:1px solid #ddd;"><strong>Email</strong></td><td style="padding:8px; border:1px solid #ddd;">' . htmlspecialchars($post['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . '</td></tr>'
                . '<tr><td style="padding:8px; border:1px solid #ddd;"><strong>Whatsapp</strong></td><td style="padding:8px; border:1px solid #ddd;">' . htmlspecialchars($post['mobile'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . '</td></tr>'
                . '<tr><td style="padding:8px; border:1px solid #ddd;"><strong>Event</strong></td><td style="padding:8px; border:1px solid #ddd;">' . htmlspecialchars($post['event_name'] ?? 'Bridal Asia', ENT_QUOTES, 'UTF-8') . '</td></tr>'
                . '<tr><td colspan="2" style="padding:8px; border:1px solid #ddd; text-align:center;">'
                . '<img src="data:' . htmlspecialchars($photoMime, ENT_QUOTES, 'UTF-8') . ';base64,' . $photoBase64 . '" style="max-width:200px; height:auto; border:1px solid #ccc;" alt="Badge Photo">'
                . '</td></tr>'
                . '</table>'
                . '<p style="margin-top:20px;">This digital badge is valid for exhibition entry. Please carry this PDF on the event day.</p>'
                . '</div>';

            try {
                $mpdf = new \Mpdf\Mpdf(['tempDir' => WRITEPATH . 'cache']);
                $mpdf->WriteHTML($badgeHtml);
                $pdfContent = $mpdf->Output('', 'S');
                send_common_mail($post['email'], 'Bridal Asia - Exhibitor Digital Badge', 'Dear ' . htmlspecialchars($post['fname'], ENT_QUOTES, 'UTF-8') . '<br><br>Your digital badge is attached as a PDF.', ['attachments' => [['name' => 'Exhibitor-Badge.pdf', 'content' => $pdfContent]]]);
            } catch (\Exception $e) {
                log_message('error', 'Badge PDF/email send failed: ' . $e->getMessage());
            }
        }
        // $this->db->table('exlogs')->insert(['exid' => $post['exid'], 'exname' => $session->get('user_name') ?? '', 'comment' => 'Exhibitor Badge Added Name: ' . $post['fname'] . ' ' . ($post['lname'] ?? ''), 'created_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true, 'message' => 'Badge created successfully.']);
    }

    public function fascia(): string
    {
        $session = session();
        $exhibitorId = $session->get('exhibitor_id');
        $subEventId = null;
        $token = $_COOKIE['api_token'] ?? '';
        if (!empty($token)) {
            try {
                $parts = explode('.', $token);
                if (count($parts) === 3) {
                    $payload = json_decode(base64_decode(str_pad(
                        strtr($parts[1], '-_', '+/'),
                        strlen($parts[1]) % 4,
                        '=',
                        STR_PAD_RIGHT
                    )), true);

                    if (!empty($payload['sub_event_id'])) {
                        $subEventId = (int) $payload['sub_event_id'];
                    }
                }
            } catch (\Exception $e) {
                log_message('error', '[fascia] Token decode failed: ' . $e->getMessage());
            }
        }
        $savedData = [];
        if ($exhibitorId) {
            $builder = $this->db->table('exhibitors')->where('id', $exhibitorId);
            if ($subEventId && $this->db->fieldExists('sub_event_id', 'exhibitors')) {
                $builder->where('sub_event_id', $subEventId);
            }
            $row = $builder->get()->getRowArray();
            if (!$row && $subEventId && $this->db->fieldExists('sub_event_id', 'exhibitors')) {
                $row = $this->db->table('exhibitors')->where('id', $exhibitorId)->get()->getRowArray();
            }
            if ($row) {
                $savedData = [
                    'shell_space' => $row['shell_space'] ?? '',
                    'stand_wording' => $row['stand_wording'] ?? '',
                    'stand_layout' => $row['stand_layout'] ?? '',
                    'electricity_load' => $row['electricity_load'] ?? '',
                    'raw_design' => $row['raw_design'] ?? '',
                    'raw_design_status' => $row['raw_design_status'] ?? '',
                    'fabricator_details' => $row['fabricator_details'] ?? '',
                ];
            }
        }
        return view('exhibitor/fascia', [
            'saved_fascia' => $savedData,
            'sub_event_id' => $subEventId,
        ]);
    }
    
    public function add_to_cart()
    {
        if ($this->request->getMethod(true) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }
        $session = session();
        $vendorId = $session->get('exhibitor_id');
        if (! $vendorId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'item_id' => 'required|integer',
            'item_name' => 'required|max_length[100]',
            'sale_price_inr' => 'permit_empty|decimal',
            'sale_price_usd' => 'permit_empty|decimal',
            'quantity' => 'required|integer|greater_than[0]',
            'item_image' => 'permit_empty|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }
        $eventId = (int) ($session->get('event_id') ?? 0);
        $cartModel = new CartModel();
        $isInternational = !empty($payload['sale_price_usd']);
        $saved = $cartModel->addToCart($vendorId, $payload, $eventId, $isInternational);
        if (! $saved) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Unable to add item']);
        }
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Item added successfully.',
        ]);
    }

    public function cart_items()
    {
        $session = session();
        $vendorId = $session->get('vendor_id');
        if (! $vendorId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $isInternational = (bool) $session->get('is_international'); // flag stored in session
        $cartModel = new CartModel();
        $items = $cartModel->getItems($vendorId, $isInternational);
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $subtotal = number_format($subtotal, 2, '.', '');
        $tax = number_format($subtotal * 0.18, 2, '.', '');
        $total = number_format($subtotal + $tax, 2, '.', '');
        return $this->response->setJSON([
            'success' => true,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    public function remove_cart_item()
    {
        if ($this->request->getMethod(true) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }
        $session = session();
        $vendorId = $session->get('vendor_id');
        if (! $vendorId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $itemId = (int) ($payload['item_id'] ?? 0);
        if ($itemId <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Invalid item']);
        }
        $cartModel = new CartModel();
        $deleted = $cartModel->removeItem($vendorId, $itemId);
        if (! $deleted) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Unable to remove item']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Item removed successfully']);
    }

    public function additional_furniture_list()
    {
        $token = $_COOKIE['api_token'] ?? '';
        $parts   = explode('.', $token);
        if (empty($token)) {
            return redirect()->to('login')->with('fail', 'Token missing. Please Login Again.');
        }
        return view('exhibitor/additional-furniture');
    }

    public function success()
    {
        return view('exhibitor/payment-success');
    }

    public function visitor_invitation()
    {
        return view('exhibitor/visitor_invitation');
    }

    public function failed()
    {
        return view('exhibitor/payment-failed');
    }
    public function getGuidelines()
    {
        return view('exhibitor/guideline-page');
    }

    public function reference_image()
    {
        return view('exhibitor/reference-image');
    }
}
