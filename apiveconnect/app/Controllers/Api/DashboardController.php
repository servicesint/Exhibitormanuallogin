<?php

namespace App\Controllers\Api;

use App\Libraries\JwtPayload;
use App\Models\ExhibitorContactPersonModel;
use App\Models\ExhibitorBadgeModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use App\Libraries\UploadHelper;
use App\Libraries\PdfHelper;

class DashboardController extends BaseController
{
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }

    private function getJwtContext(): array
    {
        $payload = JwtPayload::get();
        $subEventId = $payload->sub_event_id ?? null;
        $eventId = $payload->event_id ?? null;
        if (empty($eventId) && !empty($subEventId)) {
            $eventId = $this->resolveEventIdFromSubEvent($subEventId);
        }
        return [
            'payload'    => $payload,
            'vendorId'   => $payload->sub ?? null,
            'exhibitor_id'   => $payload->exhibitor_id ?? null,
            'subEventId' => $subEventId,
            'eventId'    => $eventId,
        ];
    }

    private function resolveEventIdFromSubEvent($subEventId): ?int
    {
        if (empty($subEventId)) {
            return null;
        }

        $row = $this->db->table('manual_setups')
            ->select('event_id')
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRow();

        if ($row && !empty($row->event_id)) {
            return (int) $row->event_id;
        }

        $row = $this->db->table('company_sub_events')
            ->select('event_id')
            ->where('id', $subEventId)
            ->get()
            ->getRow();

        return ($row && !empty($row->event_id)) ? (int) $row->event_id : null;
    }

    private function getParticipationLetterValue($table, array $columns, $id, $default = '')
    {
        if (empty($table) || empty($id)) {
            return $default;
        }
        foreach ($columns as $column) {
            try {
                $row = $this->db->table($table)
                    ->select($column)
                    ->where('id', $id)
                    ->get()
                    ->getRow();
                if ($row && !empty($row->{$column})) {
                    return (string) $row->{$column};
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return $default;
    }

    private function getParticipationLetterData(array $jwt): array
    {
        $vendorId = $jwt['vendorId'] ?? null;
        $subEventId = $jwt['subEventId'] ?? null;
        $eventId = $jwt['eventId'] ?? null;
        $data = [
            'company_name' => 'M/S SI',
            'event_year' => date('Y'),
            'event_venue' => '',
            'stall_no' => '',
            'letter_date' => date('d F Y'),
            'signatory_name' => 'Payal Paul',
            'signatory_mobile' => '+91-9354688923',
            'signatory_email' => 'ppaul@servintonline.com',
        ];
        if ($vendorId) {
            $model = new ExhibitorContactPersonModel();
            $profile = $model->getProfile($vendorId);
            if ($profile) {
                $data['company_name'] = $profile->company_name ?? $profile->company ?? $profile->exhibitor_name ?? $data['company_name'];
                $data['signatory_name'] = $profile->contact_person_name ?? $profile->name ?? $data['signatory_name'];
                $data['signatory_mobile'] = $profile->phone ?? $profile->mobile ?? $data['signatory_mobile'];
                $data['signatory_email'] = $profile->email ?? $data['signatory_email'];
            }
        }
        if ($subEventId) {
            $data['stall_no'] = $this->getParticipationLetterValue('company_sub_events', ['stall_no', 'stall_number', 'stall_number_allocated', 'stand_no'], $subEventId, $data['stall_no']);
            $data['event_venue'] = $this->getParticipationLetterValue('company_sub_events', ['venue', 'venue_name', 'location'], $subEventId, $data['event_venue']);
            $data['event_year'] = $this->getParticipationLetterValue('company_sub_events', ['event_year', 'year', 'event_year_value'], $subEventId, $data['event_year']);
        }
        if ($eventId) {
            $data['event_venue'] = $this->getParticipationLetterValue('manual_setups', ['venue', 'venue_name', 'location'], $eventId, $data['event_venue']);
            $data['stall_no'] = $this->getParticipationLetterValue('manual_setups', ['stall_no', 'stall_number', 'stall_number_allocated', 'stand_no'], $eventId, $data['stall_no']);
            $data['event_year'] = $this->getParticipationLetterValue('manual_setups', ['event_year', 'year'], $eventId, $data['event_year']);
        }
        return $data;
    }

    public function participationLetter()
    {
        $jwt = $this->getJwtContext();
        $data = $this->getParticipationLetterData($jwt);
        return $this->response->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Participation letter data fetched successfully.',
            'data' => $data,
        ]);
    }

    private function getFasciaTableData(array $post, array $extra = [], string $tableName = 'stall_categories'): array
    {
        static $columnsCache = [];
        if (!isset($columnsCache[$tableName])) {
            $columnsCache[$tableName] = $this->db->getFieldNames($tableName);
        }
        $columns = $columnsCache[$tableName];
        $columnMap = [];
        foreach ($columns as $column) {
            $columnMap[strtolower($column)] = $column;
        }
        $data = [];
        foreach ($post as $field => $value) {
            $key = strtolower($field);
            if (isset($columnMap[$key])) {
                $data[$columnMap[$key]] = $value;
            }
        }
        foreach ($extra as $field => $value) {
            $key = strtolower($field);
            if (isset($columnMap[$key])) {
                $data[$columnMap[$key]] = $value;
            }
        }

        // --- FIXED THE BUG HERE ---
        foreach (['first_name', 'last_name', 'mobile_number', 'email', 'address', 'stall_layout'] as $field) {
            if (array_key_exists($field, $post) && isset($columnMap[strtolower($field)])) {
                // ONLY overwrite if the submitted value is NOT empty. 
                // This prevents an empty string from deleting an existing file path.
                if (!empty($post[$field])) {
                    $data[$columnMap[strtolower($field)]] = $post[$field];
                }
            }
        }

        return $data;
    }

    public function exhibitor_dashboard()
    {
        $jwt = $this->getJwtContext();
        $subEventId = $jwt['subEventId'];
        if (!$subEventId) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => false, 'message' => 'Invalid token: missing sub_event_id.']);
        }
        $model = new ExhibitorContactPersonModel();
        $event = $model->getById('manual_setups', ['sub_event_id' => $subEventId], 'manual_welcome_note');
        if (!$event) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => false, 'message' => 'Event data not found.']);
        }
        return $this->response->setJSON([
            'status'       => true,
            'sub_event_id' => $subEventId,
            'welcome_note' => $event->manual_welcome_note ?? '',
        ]);
    }

    public function profile_index()
    {
        $jwt = $this->getJwtContext();
        $subEventId = $jwt['subEventId'];
        $userId     = $jwt['vendorId'];

        if (!$subEventId) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'code'    => 400,
                    'message' => 'Invalid token.',
                    'data'    => null
                ]);
        }
        $model   = new ExhibitorContactPersonModel();
        $profile = $model->getProfile($userId);
        if (!$profile) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Profile not found.',
                    'data'    => null
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Profile fetched successfully.',
                'data'    => $profile
            ]);
    }

    public function fascia()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['vendorId'];
        $subEventId = $jwt['subEventId'];
        $eventId = $jwt['eventId'];
        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
            ->join('exhibitors as e', 'e.id = ecp.exhibitor_id', 'left')
            ->join('stall_categories as sc', 'sc.exhibitor_id = ecp.exhibitor_id', 'left')
            ->join('manual_setups as ms', 'ms.sub_event_id = sc.sub_event_id', 'left')
            ->select('e.id as exhibitor_id, e.stall_type_id, e.exhibitor_type, sc.electricity_requirement, sc.stall_layout as fascia_design, sc.status as fascia_design_status, sc.stall_open_side, sc.fascia_board_text, sc.salutation, sc.first_name, sc.last_name, sc.fabricator_company_name, sc.mobile_number, sc.email, sc.reason, sc.other_reason, ms.manual_fascia_note')
            ->where('ecp.id', $vendorId)
            ->get()->getRowArray();
        $exhibitorId = $exhibitor['exhibitor_id'] ?? null;
        if (!$exhibitorId) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => false, 'code' => 404, 'message' => 'Exhibitor not found.', 'data' => null]);
        }
        $builder = $this->db->table('stall_categories as sc')
            ->where('sc.exhibitor_id', $exhibitorId);
        if ($subEventId) {
            $builder->where('sc.sub_event_id', $subEventId);
        }
        if ($eventId) {
            $builder->where('sc.event_id', $eventId);
        }
        $row = $builder->get()->getRowArray();
        $data = [];
        if ($row) {
            $data = [
                'stall_type_id' => $exhibitor['stall_type_id'] ?? '',
                'stall_open_side' => $exhibitor['stall_open_side'] ?? '',
                'fascia_board_text' => $exhibitor['fascia_board_text'] ?? '',
                'electricity_requirement' => $exhibitor['electricity_requirement'] ?? '',
                'fascia_design' => $exhibitor['fascia_design'] ?? '',
                'fascia_design_status' => $exhibitor['fascia_design_status'] ?? '',
                'exhibitor_type' => $exhibitor['exhibitor_type'] ?? '',
                'salutation' => $exhibitor['salutation'] ?? '',
                'first_name' => $row['first_name'] ?? '',
                'last_name' => $row['last_name'] ?? '',
                'fabricator_company_name' => $row['fabricator_company_name'] ?? '',
                'mobile_number' => $row['mobile_number'] ?? '',
                'email' => $row['email'] ?? '',
                'status' => $row['status'] ?? '',
                'address' => $row['address'] ?? '',
                'reason' => $row['reason'] ?? '',
                'other_reason' => $row['other_reason'] ?? '',
            ];
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status' => true,
                'code' => 200,
                'message' => 'Fascia data fetched successfully.',
                'data' => $data,
                'raw_text' => $exhibitor['manual_fascia_note'] ?? ''
            ]);
    }

    public function saveFascia()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['vendorId'];
        $subEventId = $jwt['subEventId'];
        $eventId = $jwt['eventId'];

        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }

        $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
            ->join('exhibitors as e', 'e.id = ecp.exhibitor_id', 'left')
            ->select('e.id as exhibitor_id, e.stall_type_id')
            ->where('ecp.id', $vendorId)
            ->get()
            ->getRowArray();

        $exhibitorId = $exhibitor['exhibitor_id'] ?? null;
        if (!$exhibitorId) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => false, 'code' => 404, 'message' => 'Exhibitor not found.', 'data' => null]);
        }

        $post = $this->request->getPost() ?: [];

        $allow = [
            'stall_open_side',
            'fascia_board_text',
            'invitation_tickets',
            'add_tickets',
            'fascia_category',
            'profile',
            'chief_executive',
            'specialization',
            'products_display',
            'electricity_requirement',
            'fascia_design_status',
            'salutation',
            'first_name',
            'last_name',
            'fabricator_company_name',
            'mobile_number',
            'email',
            'address',
            'fabricator_details',
            'stall_layout',
            'country_code',
            'reason',
            'other_reason',
            'hodi_emails'
        ];

        $filteredPost = [];
        foreach ($post as $field => $val) {
            if (in_array($field, $allow, true)) {
                $filteredPost[$field] = $val;
            }
        }
        $post = $filteredPost;
        $tableName = 'stall_categories';
        $builder = $this->db->table($tableName)->where('exhibitor_id', $exhibitorId);
        if ($subEventId) {
            $builder->where('sub_event_id', $subEventId);
        }
        if ($eventId) {
            $builder->where('event_id', $eventId);
        }
        $existing = $builder->get()->getRowArray();
        $file = $this->request->getFile('fascia_design');

        $isNewFileUploaded = false;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!empty($existing['stall_layout'])) {
                UploadHelper::delete($existing['stall_layout'], 'fascias');
            }
            // SAVE THE FILE PATH TEMPORARILY
            $post['stall_layout'] = UploadHelper::upload($file, 'fascias', 'fascia_design');
            $post['fascia_design_status'] = '1';
            $isNewFileUploaded = true;
        }

        $category = $post['fascia_category'] ?? '';

        if ($category === 'Shell Space') {
            if (!$file || !$file->isValid()) {
                $post['stall_layout'] = '';
            }
            $post['fascia_design_status'] = '0';
            $post['electricity_requirement'] = '';
            $post['salutation'] = '';
            $post['first_name'] = '';
            $post['last_name'] = '';
            $post['fabricator_company_name'] = '';
            $post['mobile_number'] = '';
            $post['email'] = '';
            $post['address'] = '';
            $post['country_code'] = '';
            $post['reason'] = '';
            $post['other_reason'] = '';
            $post['hodi_emails'] = '';
        } elseif ($category === 'Raw Space') {
            $post['stall_open_side'] = '';
            $post['fascia_board_text'] = '';
        }

        $extraData = [
            'exhibitor_id' => $exhibitorId,
            'sub_event_id' => $subEventId,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($eventId) {
            $extraData['event_id'] = $eventId;
        }

        // Har naye file upload par status ko 'pending' pe reset karo,
        // taaki admin ko dubara review karna pade
        if ($isNewFileUploaded) {
            $extraData['status'] = 'pending';
        }

        $data = $this->getFasciaTableData($post, $extraData, $tableName);
        if (isset($post['stall_layout'])) {
            $data['stall_layout'] = $post['stall_layout'];
        }

        if ($existing) {
            $matchBuilder = $this->db->table($tableName);
            $matchBuilder->where('exhibitor_id', $exhibitorId);
            if ($subEventId) {
                $matchBuilder->where('sub_event_id', $subEventId);
            }
            if ($eventId) {
                $matchBuilder->where('event_id', $eventId);
            }
            $updated = $matchBuilder->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $updated = $this->db->table($tableName)->insert($data);
        }

        return $this->response
            ->setStatusCode($updated ? 200 : 500)
            ->setJSON([
                'status' => (bool) $updated,
                'code' => $updated ? 200 : 500,
                'message' => $updated ? 'Your information submitted successfully.' : 'Something went wrong. Please try again.',
                'data' => $updated ? ['fascia_category' => $post['fascia_category'] ?? ''] : null,
            ]);
    }

    public function add_to_cart()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['exhibitor_id'];
        $subEventId = $jwt['subEventId'];
        if (!$vendorId || !$subEventId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $contentType = $this->request->getHeaderLine('Content-Type');
        $input = str_contains($contentType, 'application/json')
            ? ($this->request->getJSON(true) ?? [])
            : $this->request->getPost();
        $rules = [
            'item_id'  => 'required|integer',
            'quantity' => 'required|integer|greater_than[0]',
        ];
        if (!$this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'code' => 422, 'message' => 'Validation failed.', 'data' => $this->validator->getErrors()]);
        }
        $itemId   = (int) $input['item_id'];
        $quantity = (int) $input['quantity'];
        $item = $this->db->table('items')
            ->where('id', $itemId)
            ->where('sub_event_id', $subEventId)
            ->where('is_deleted', 0)
            ->get()
            ->getRow();
        if (!$item) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => false, 'code' => 404, 'message' => 'Item not found.', 'data' => null]);
        }
        $isInternational = $this->resolveIsInternational($vendorId);
        $priceData   = $this->resolveItemPrice($item, $isInternational);
        $price       = $priceData['price'];
        $salePrice   = $priceData['sale_price'];
        $isEarlyBird = $priceData['is_early_bird'];
        if ($price <= 0) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'code' => 422, 'message' => 'Item price is not set.', 'data' => null]);
        }
        $cartModel = new CartModel();
        $saved     = $cartModel->addToCart(
            $vendorId,
            $itemId,
            $subEventId,
            $quantity,
            $price,
            $salePrice,
            $isEarlyBird
        );
        if (!$saved) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => false, 'code' => 500, 'message' => 'Unable to add item.', 'data' => null]);
        }
        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Item added successfully.',
                'data'    => [
                    'item_id'        => $itemId,
                    'item_name'      => $item->item_name,
                    'quantity'       => $quantity,
                    'price'          => $price,
                    'sale_price'     => $salePrice,
                    'is_early_bird'  => $isEarlyBird,
                    'item_image'     => $item->item_image ?? '',
                ]
            ]);
    }

    private function resolveItemPrice(object $item, bool $isInternational): array
    {
        $today          = date('Y-m-d');
        $isEarlyBird    = !empty($item->early_bird_date) && ($today <= $item->early_bird_date);
        $salePrice      = $isInternational ? (float) $item->sale_price_usd : (float) $item->sale_price_inr;
        $earlyBirdPrice = $isInternational ? (float) $item->early_bird_price_usd : (float) $item->early_bird_price_inr;
        if ($isEarlyBird && $earlyBirdPrice <= 0) {
            $isEarlyBird = false;
        }
        $activePrice = $isEarlyBird ? $earlyBirdPrice : $salePrice;
        return [
            'price'           => round($activePrice, 2),
            'sale_price'      => round($salePrice, 2),
            'is_early_bird'   => $isEarlyBird,
            'hike_percentage' => 0,
        ];
    }

    public function cart_items()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['exhibitor_id'];
        $subEventId = $jwt['subEventId'];
        if (!$vendorId || !$subEventId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $isInternational = $this->resolveIsInternational($vendorId);
        $currencySymbol  = $isInternational ? '$' : '₹';
        $cartModel = new CartModel();
        $items     = $cartModel->getItems($vendorId, $isInternational);
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $subtotal = number_format($subtotal, 2, '.', '');
        $tax      = number_format($subtotal * 0.18, 2, '.', '');
        $total    = number_format((float)$subtotal + (float)$tax, 2, '.', '');
        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'   => true,
                'code'     => 200,
                'message'  => 'Cart items fetched successfully.',
                'data'     => [
                    'items'           => $items,
                    'subtotal'        => $subtotal,
                    'tax'             => $tax,
                    'total'           => $total,
                    'currency_symbol' => $currencySymbol,
                ]
            ]);
    }

    public function remove_cart_item()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['exhibitor_id'];
        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $contentType = $this->request->getHeaderLine('Content-Type');
        $input = str_contains($contentType, 'application/json') ? ($this->request->getJSON(true) ?? []) : $this->request->getPost();
        $itemId = (int) ($input['item_id'] ?? 0);
        if ($itemId <= 0) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'code' => 422, 'message' => 'Invalid item.', 'data' => null]);
        }
        $cartModel = new CartModel();
        $deleted   = $cartModel->removeItem($vendorId, $itemId);
        if (!$deleted) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => false, 'code' => 500, 'message' => 'Unable to remove item.', 'data' => null]);
        }
        return $this->response
            ->setStatusCode(200)
            ->setJSON(['status' => true, 'code' => 200, 'message' => 'Item removed successfully.', 'data' => null]);
    }

    public function additional_furniture()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['vendorId'];
        $subEventId = $jwt['subEventId'];
        $exhibitor_id = $jwt['exhibitor_id'];
        if (!$vendorId || !$subEventId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }

        $resolved = $this->newresolveIsInternational($exhibitor_id, $subEventId);
        $isInternational = $resolved['is_international'];

        $currencySymbol  = $isInternational ? '$' : '₹';
        try {
            $items = $this->db->table('items')
                ->select('id, item_name, item_image, early_bird_date, early_bird_price_inr, early_bird_price_usd, sale_price_inr, sale_price_usd, description')
                ->where('sub_event_id', $subEventId)
                ->where('is_deleted', 0)
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[additional_furniture] ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => false, 'code' => 500, 'message' => 'Failed to load furniture list.', 'data' => null]);
        }
        $today = date('Y-m-d');
        $inventory = array_map(function ($item) use ($today, $isInternational) {
            $isEarlyBird = !empty($item['early_bird_date']) && ($today <= $item['early_bird_date']);
            $salePrice = $isInternational ? (float) $item['sale_price_usd'] : (float) $item['sale_price_inr'];
            $price = $isEarlyBird
                ? ($isInternational ? (float) $item['early_bird_price_usd'] : (float) $item['early_bird_price_inr'])
                : $salePrice;
            return [
                'id'            => $item['id'],
                'item_name'     => $item['item_name'],
                'item_image'    => $item['item_image'],
                'price'         => $price,
                'sale_price'    => $salePrice,
                'is_early_bird' => $isEarlyBird,
                'description' => $item['description'],
            ];
        }, $items);
        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Furniture list fetched successfully.',
                'data'    => [
                    'inventory'       => $inventory,
                    'currency_symbol' => $currencySymbol,
                    'is_need_additional_furniture'    => $resolved['is_need_additional_furniture'] ?? 0,
                ]
            ]);
    }

    private function newresolveIsInternational(int $exhibitor_id, ?int $subEventId = null): array
    {
        $vendor = $this->db->table('exhibitor_contact_persons ecp')
            ->select('e.exhibitor_type, e.is_need_additional_furniture')
            ->join('exhibitors e', 'e.id = ecp.exhibitor_id', 'left')
            ->where('ecp.exhibitor_id', $exhibitor_id)
            ->where('e.sub_event_id', $subEventId)
            ->get()
            ->getRow();

        if (!$vendor) {
            return [
                'exhibitor_type' => null,
                'is_need_additional_furniture' => 0,
                'is_international' => false
            ];
        }

        return [
            'exhibitor_type' => $vendor->exhibitor_type ?? null,
            'is_need_additional_furniture' => (int) ($vendor->is_need_additional_furniture ?? 0),
            'is_international' => strtolower(trim($vendor->exhibitor_type ?? '')) === 'international'
        ];
    }

    public function update_quantity()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['vendorId'];
        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $input    = $this->request->getJSON(true) ?? $this->request->getPost();
        $cartId   = (int) ($input['cart_id'] ?? 0);
        $quantity = (int) ($input['quantity'] ?? 0);
        if ($cartId <= 0) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'code' => 422, 'message' => 'Invalid cart item.', 'data' => null]);
        }
        $cartModel = new CartModel();
        $updated   = $cartModel->updateQuantity($vendorId, $cartId, $quantity);
        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => $updated,
                'code'    => 200,
                'message' => $updated ? 'Quantity updated.' : 'Failed to update quantity.',
                'data'    => null
            ]);
    }

    public function clear_cart()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['vendorId'];
        $subEventId = $jwt['subEventId'];
        if (!$vendorId || !$subEventId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $cartModel = new CartModel();
        $cartModel->clearCart($vendorId, $subEventId);
        return $this->response
            ->setStatusCode(200)
            ->setJSON(['status' => true, 'code' => 200, 'message' => 'Cart cleared.', 'data' => null]);
    }

    private function resolveIsInternational(int $vendorId): bool
    {
        $vendor = $this->db->table('exhibitor_contact_persons ecp')
            ->select('e.exhibitor_type,e.is_need_additional_furniture')
            ->join('exhibitors e', 'e.id = ecp.exhibitor_id', 'left')
            ->where('e.id', $vendorId)
            ->get()
            ->getRow();
        if (!$vendor) {
            return false;
        }
        return strtolower(trim($vendor->exhibitor_type ?? '')) === 'international';
    }

    private function saveOrderPaymentDetails(int $orderId, array $paymentData): void
    {
        if ($orderId <= 0) {
            return;
        }
        $orderModel = new OrderModel();
        $updated = $orderModel->update($orderId, $paymentData);
        if ($updated === false) {
            $this->db->table('orders')->where('id', $orderId)->update($paymentData);
        }
    }

    public function generate_quotation()
    {
        try {
            $jwt = $this->getJwtContext();
            $vendorId = $jwt['exhibitor_id'];
            $subEventId = $jwt['subEventId'];
            if (!$vendorId || !$subEventId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 401,
                        'message' => 'Unauthorized.',
                        'data'    => null
                    ]);
            }
            $isInternational = $this->resolveIsInternational($vendorId);

            $currencySymbol  = $isInternational ? '$' : '₹';
            $currencyText    = $isInternational ? 'USD' : 'INR';
            $contactModel = new ExhibitorContactPersonModel();
            $profile      = $contactModel->getProfile($vendorId);

            $cartModel = new CartModel();
            $items     = $cartModel->getItems($vendorId, $isInternational);
            if (empty($items)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 422,
                        'message' => 'Cart is empty.',
                        'data'    => null
                    ]);
            }
            $subtotal = 0;
            foreach ($items as $item) {
                $price    = isset($item['price']) ? (float) $item['price'] : 0;
                $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 1;
                $subtotal += $price * $quantity;
            }
            $subtotal = round($subtotal, 2);
            $tax      = round($subtotal * 0.18, 2);

            $subEvent = $this->db->table('manual_setups')
                ->where('sub_event_id', $subEventId)
                ->get()
                ->getRow();

            $subEvents = $this->db->table('company_sub_events')
                ->where('id', $subEventId)
                ->get()
                ->getRow();

            $exhibitorInfo = $this->getExhibitorTaxInfo($vendorId);

            $gstBreakdown = $this->resolveGstBreakdown(
                $tax,
                $exhibitorInfo['name'] ?? null,
                $subEvents->venue_state ?? null,
                $isInternational
            );
            $cgst = $gstBreakdown['cgst'];
            $sgst = $gstBreakdown['sgst'];
            $igst = $gstBreakdown['igst'];
            $isSameState = $gstBreakdown['is_same_state'];
            $total = round($subtotal + $cgst + $sgst + $igst, 2);

            $eventName = '';
            if (!empty($subEvents->sub_event_name)) {
                $eventName = $subEvents->sub_event_name;
            }

            $yearStart = (int) date('y');
            if ((int) date('m') < 4) {
                $yearStart--;
            }
            $yearEnd = $yearStart + 1;
            $qid       = rand(1000, 9999);
            $invoiceNo = 'SI/PI/' . sprintf('%02d-%02d', $yearStart, $yearEnd) . '/' . $qid;
            $date      = date('d.m.Y');

            $quoteData = [
                'event_id'     => $subEventId,
                'exhibitor_id' => $vendorId,
                'qid'          => $qid,
                'q_amount'     => (string) $subtotal,
                'amount'       => (string) $total,
                'ref_no'       => $invoiceNo,
                'remarks'      => 'Proforma invoice',
                'status'       => 0,
                'currency'     => $currencySymbol,
            ];
            $quoteInserted = $this->db->table('quotes')->insert($quoteData);
            if (!$quoteInserted) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 500,
                        'message' => 'Failed to save quotation.',
                        'data'    => null
                    ]);
            }

            foreach ($items as $item) {
                $detailData = [
                    'qid'           => $qid,
                    'item_id'       => $item['id'] ?? 0,
                    'item_name'     => $item['item_name'] ?? '',
                    'quantity'      => $item['quantity'] ?? 1,
                    'unit_price'    => (string) $item['price'],
                    'sale_price'    => (string) ($item['sale_price'] ?? $item['price']),
                    'line_total'    => (string) ($item['price'] * $item['quantity']),
                    'is_early_bird' => $item['is_early_bird'] ?? 0,
                    'item_image'    => $item['item_image'] ?? null,
                ];
                $this->db->table('quotes_details')->insert($detailData);
            }

            $cartModel->clearCart($vendorId, $subEventId);

            $companyInfo = $this->db->table('companies')
                ->select('company_name, company_logo')
                ->where('id', 1)
                ->get()
                ->getRowArray();

            $invoiceData = [
                'invoice_no'       => $invoiceNo,
                'signature'        => $subEvent->signature ?? '',
                'date'             => $date,
                'profile'          => $profile,
                'items'            => $items,
                'subtotal'         => $subtotal,
                'company_name'     => $companyInfo['company_name'],
                'company_image'    => $companyInfo['company_logo'],
                'exhibitor_type'   => $exhibitorInfo['exhibitor_type'],
                'cgst'             => $cgst,
                'sgst'             => $sgst,
                'igst'             => $igst,
                'is_same_state'    => $isSameState,
                'total'            => $total,
                'currency_symbol'  => $currencySymbol,
                'currency_text'    => $currencyText,
                'event_name'       => $eventName,
                'customer_name'    => $exhibitorInfo['organisation_name'] ?? 'M/s Services International',
                'customer_gstin'   => $exhibitorInfo['gst_number'] ?? 'N/A',
                'customer_address' => $exhibitorInfo['address'] ?? '',
            ];

            $html = $this->quotationInvoiceHtml($invoiceData);
            $tempDir = WRITEPATH . 'mpdf';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            $mpdf = new Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 10,
                'margin_bottom' => 10,
                'default_font'  => 'dejavusans',
                'tempDir'       => $tempDir,
            ]);
            $mpdf->WriteHTML($html);
            $fileName = 'Additional-Furniture-Quotation-' . str_replace('/', '-', $invoiceNo) . '.pdf';
            $pdfContent = $mpdf->Output($fileName, Destination::STRING_RETURN);

            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->setHeader('Content-Length', strlen($pdfContent))
                ->setBody($pdfContent);
        } catch (\Throwable $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'code'    => 500,
                    'message' => 'Something went wrong while generating quotation PDF.',
                    'error'   => $e->getMessage(),
                    'data'    => null
                ]);
        }
    }

    private function getExhibitorTaxInfo(int $exhibitorId): array
    {
        $row = $this->db->table('exhibitors as e')
            ->select('e.organisation_name, e.gst_number, e.organisation_address as address, e.exhibitor_type, s.name')
            ->join('states as s', 's.id = e.state_id', 'left')
            ->where('e.id', $exhibitorId)
            ->get()
            ->getRowArray();
        return $row ?: [
            'organisation_name' => null,
            'gst_number'        => null,
            'address'           => null,
            'name'              => null,
            'exhibitor_type'    => null,
        ];
    }

    private function resolveGstBreakdown(
        float $taxAmount,
        ?string $exhibitorStateName,
        ?string $venueState,
        bool $isInternational = false
    ): array {
        if ($isInternational) {
            return [
                'cgst'          => 0.00,
                'sgst'          => 0.00,
                'igst'          => round($taxAmount, 2),
                'is_same_state' => false,
            ];
        }
        $isSameState = $exhibitorStateName !== null
            && $venueState !== null
            && strtolower(trim($exhibitorStateName)) === strtolower(trim($venueState));
        if ($isSameState) {
            return [
                'cgst'          => round($taxAmount / 2, 2),
                'sgst'          => round($taxAmount / 2, 2),
                'igst'          => 0.00,
                'is_same_state' => true,
            ];
        }
        return [
            'cgst'          => 0.00,
            'sgst'          => 0.00,
            'igst'          => round($taxAmount, 2),
            'is_same_state' => false,
        ];
    }

    private function quotationInvoiceHtml(array $invoiceData)
    {
        return view('performa-invoice', $invoiceData);
    }

    private function quotationInvoiceHtml2(array $invoiceData)
    {
        return view('quotation', $invoiceData);
    }

    public function get_quotations()
    {
        $payload  = JwtPayload::get();
        $vendorId = $payload->exhibitor_id ?? null;
        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        try {
            $quotations = $this->db->table('quotes')
                ->select('id, qid, ref_no, q_amount, amount, status, added_date')
                ->where('exhibitor_id', $vendorId)
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'  => true,
                    'code'    => 200,
                    'message' => 'Quotations fetched successfully.',
                    'data'    => ['quotations' => $quotations]
                ]);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching quotations: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => false, 'code' => 500, 'message' => 'Failed to fetch quotations.', 'data' => null]);
        }
    }

    public function get_quotation_details($qid = null)
    {
        $payload  = JwtPayload::get();
        $vendorId = $payload->exhibitor_id ?? null;
        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        if (!$qid) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'code' => 422, 'message' => 'Quotation ID required.', 'data' => null]);
        }
        try {
            $quote = $this->db->table('quotes')
                ->where('qid', $qid)
                ->where('exhibitor_id', $vendorId)
                ->get()
                ->getRow();
            if (!$quote) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['status' => false, 'code' => 404, 'message' => 'Quotation not found.', 'data' => null]);
            }
            $details = $this->db->table('quotes_details')
                ->where('qid', $qid)
                ->get()
                ->getResultArray();
            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'  => true,
                    'code'    => 200,
                    'message' => 'Quotation details fetched successfully.',
                    'data'    => [
                        'quote'   => $quote,
                        'items'   => $details
                    ]
                ]);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching quotation details: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => false, 'code' => 500, 'message' => 'Failed to fetch quotation details.', 'data' => null]);
        }
    }

    public function update_quotation()
    {
        $payload  = JwtPayload::get();
        $exhibitor_id = $payload->exhibitor_id ?? null;

        if (!$exhibitor_id) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $contentType = $this->request->getHeaderLine('Content-Type');
        $input = str_contains($contentType, 'application/json')
            ? ($this->request->getJSON(true) ?? [])
            : $this->request->getPost();

        $qid            = (int) ($input['qid'] ?? 0);
        $amountTransfer = $input['amount_transfer'] ?? null;
        $referenceNo    = $input['reference_no'] ?? null;
        $reason         = $input['reason_for_difference'] ?? null;

        if ($qid <= 0) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'code' => 422, 'message' => 'Invalid quotation ID.', 'data' => null]);
        }

        if (empty($referenceNo)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'code' => 422, 'message' => 'Reference number is required.', 'data' => null]);
        }

        try {
            $quote = $this->db->table('quotes')
                ->where('qid', $qid)
                ->where('exhibitor_id', $exhibitor_id)
                ->get()
                ->getRow();
            if (!$quote) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['status' => false, 'code' => 404, 'message' => 'Quotation not found.', 'data' => null]);
            }
            $updateData = [
                'ref_no'       => $referenceNo,
                'amount'       => $amountTransfer ?? $quote->amount,
                'remarks'      => $reason,
                'status'       => 2,
                'updated_date' => date('Y-m-d H:i:s')
            ];
            $this->db->table('quotes')
                ->where('qid', $qid)
                ->where('exhibitor_id', $exhibitor_id)
                ->update($updateData);

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'  => true,
                    'code'    => 200,
                    'message' => 'NEFT transfer saved successfully.',
                    'data'    => [
                        'qid'        => $qid,
                        'ref_no'     => $referenceNo,
                        'amount'     => $amountTransfer ?? $quote->amount,
                        'status'     => 2
                    ]
                ]);
        } catch (\Exception $e) {
            log_message('error', 'NEFT transfer error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => false, 'code' => 500, 'message' => 'Failed to save NEFT transfer.', 'data' => null]);
        }
    }

    protected function getInput(): array
    {
        $contentType = $this->request->getHeaderLine('Content-Type');
        if (str_contains($contentType, 'application/json')) {
            $json = $this->request->getJSON(true);
            if (is_array($json)) {
                return $json;
            }
        }

        $post = $this->request->getPost();
        if (is_array($post) && $post !== []) {
            return $post;
        }

        $rawBody = (string) file_get_contents('php://input');
        if ($rawBody !== '') {
            parse_str($rawBody, $parsedBody);
            if (is_array($parsedBody) && $parsedBody !== []) {
                return $parsedBody;
            }
        }
        return $this->request->getGet() ?: [];
    }

    private function resolvePaymentValue(array $input, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!empty($input[$key])) {
                return (string) $input[$key];
            }
        }
        return null;
    }

    private function isAjaxRequest(): bool
    {
        $requestedWith = strtolower($this->request->getHeaderLine('X-Requested-With'));
        if ($requestedWith !== '') {
            return $requestedWith === 'xmlhttprequest';
        }
        $contentType = $this->request->getHeaderLine('Content-Type');
        return str_contains($contentType, 'application/json');
    }

    private function verifyRazorpaySignature(string $orderId, string $paymentId, string $signature): bool
    {
        $secret = getenv('RAZORPAY_KEY_SECRET');
        if (!$secret || !$orderId || !$paymentId || !$signature) {
            return false;
        }
        $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    public function checkout()
    {
        $payload    = JwtPayload::get();
        $vendorId   = $payload->exhibitor_id ?? null;
        $subEventId = $payload->sub_event_id ?? null;

        if (!$vendorId || !$subEventId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
        }

        $input = $this->getInput();

        $isInternational = $this->resolveIsInternational($vendorId);

        $currency = $isInternational
            ? (getenv('RAZORPAY_INTERNATIONAL_CURRENCY') ?: 'USD')
            : (getenv('RAZORPAY_DOMESTIC_CURRENCY') ?: 'INR');

        $cartModel = new CartModel();
        $items = $cartModel->getItems($vendorId, $isInternational);

        if (empty($items)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status'  => false,
                    'code'    => 422,
                    'message' => 'Your cart is empty.',
                    'data'    => null,
                ]);
        }

        $outOfStockItems = array_filter($items, function ($item) {
            $flag = $item['is_deleted'] ?? 0;
            return $flag === true || $flag === 1 || $flag === '1';
        });

        if (!empty($outOfStockItems)) {
            $outOfStockNames = array_map(function ($item) {
                return $item['item_name'] ?? 'Unknown item';
            }, $outOfStockItems);

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status'  => false,
                    'code'    => 422,
                    'message' => 'Your cart contains out-of-stock items: ' . implode(', ', $outOfStockNames) . '. Please remove them before proceeding.',
                    'data'    => null,
                ]);
        }

        $subtotal = 0;

        foreach ($items as $item) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            $subtotal += $price * $quantity;
        }

        $subtotal = round($subtotal, 2);
        $tax      = round($subtotal * 0.18, 2);
        $total    = round($subtotal + $tax, 2);

        if ($total <= 0) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status'  => false,
                    'code'    => 422,
                    'message' => 'Invalid cart amount.',
                    'data'    => null,
                ]);
        }

        $keyId     = getenv('RAZORPAY_KEY_ID');
        $keySecret = getenv('RAZORPAY_KEY_SECRET');

        if (!$keyId || !$keySecret) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'code'    => 500,
                    'message' => 'Razorpay keys are not configured.',
                    'data'    => null,
                ]);
        }

        $successUrl = $this->sanitizeRedirectUrl($input['success_url'] ?? getenv('RAZORPAY_SUCCESS_URL'));
        $failedUrl = $this->sanitizeRedirectUrl($input['failed_url'] ?? getenv('RAZORPAY_FAILED_URL'));
        $callbackUrl = $input['callback_url'] ?? getenv('RAZORPAY_CALLBACK_URL');

        if (!$successUrl || !$failedUrl || !$callbackUrl) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'code'    => 500,
                    'message' => 'Payment URLs are not configured.',
                    'data'    => null,
                ]);
        }

        try {
            $api = new Api($keyId, $keySecret);

            $razorpayOrder = $api->order->create([
                'amount'          => (int) round($total * 100),
                'currency'        => $currency,
                'receipt'         => 'cart_' . $vendorId . '_' . time(),
                'payment_capture' => 1,
                'notes'           => [
                    'exhibitor_id'        => (string) $vendorId,
                    'sub_event_id'     => (string) $subEventId,
                    'is_international' => $isInternational ? '1' : '0',
                ],
            ]);

            $orderModel = new OrderModel();

            $orderId = $orderModel->createOrderFromCart(
                $vendorId,
                $subEventId,
                $items,
                [
                    'subtotal' => $subtotal,
                    'tax'      => $tax,
                    'total'    => $total,
                    'currency' => $currency,
                ],
                $isInternational,
                [
                    'payment_method'       => 'razorpay',
                    'quotation_amount'     => $input['quotation_amount'] ?? $total,
                    'payment_status'       => 'pending',
                    'razorpay_order_id'    => $razorpayOrder['id'],
                    'payment_success_url'  => $successUrl,
                    'payment_failed_url'   => $failedUrl,
                    'payment_callback_url' => $callbackUrl,
                ]
            );

            if (!$orderId) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 500,
                        'message' => 'Failed to create order.',
                        'data'    => null,
                    ]);
            }

            $this->saveOrderPaymentDetails((int) $orderId, [
                'payment_method'    => 'razorpay',
                'payment_status'    => 'pending',
                'payment_reference' => $razorpayOrder['id'],
                'razorpay_order_id' => $razorpayOrder['id'],
                'razorpay_payment_id' => null,
                'razorpay_signature'  => null,
                'payment_currency'  => $currency,
                'payment_amount'    => $total,
                'payment_response'  => json_encode([
                    'receipt' => 'cart_' . $vendorId . '_' . time(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'notes' => [
                        'vendor_id' => (string) $vendorId,
                        'sub_event_id' => (string) $subEventId,
                        'is_international' => $isInternational ? '1' : '0',
                    ],
                ]),
            ]);

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'  => true,
                    'code'    => 200,
                    'message' => 'Razorpay order created successfully.',
                    'data'    => [
                        'order_id'             => $orderId,
                        'razorpay_order_id'    => $razorpayOrder['id'],
                        'razorpay_key'         => $keyId,
                        'amount'               => $razorpayOrder['amount'],
                        'display_amount'       => $total,
                        'currency'             => $razorpayOrder['currency'],
                        'is_international'     => $isInternational,
                        'callback_url'         => $callbackUrl,
                        'success_url'          => $successUrl,
                        'failed_url'           => $failedUrl,
                    ],
                ]);
        } catch (\Throwable $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'code'    => 500,
                    'message' => 'Unable to create Razorpay order.',
                    'data'    => null,
                ]);
        }
    }
    private function sanitizeRedirectUrl(?string $url): ?string
    {
        if (!$url) {
            return $url;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return $url;
        }

        // Agar domain URL path mein doubled hai (e.g. host/host/...), to ek baar clean karo
        $doubledPattern = '#(https?://' . preg_quote($host, '#') . ')/' . preg_quote($host, '#') . '#i';
        return preg_replace($doubledPattern, '$1', $url);
    }
    public function past_orders()
    {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['exhibitor_id'];
        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $orderModel = new OrderModel();
        $orders     = $orderModel->getOrdersByVendor($vendorId);
        foreach ($orders as &$order) {
            $order['enc_id'] = encryptData($order['id']);
        }
        unset($order);

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Orders fetched successfully.',
                'data'    => ['orders' => $orders]
            ]);
    }

    public function order_detail()
    {


        $jwt = $this->getJwtContext();
        $vendorId = $jwt['exhibitor_id'];

        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $id = $this->request->getUri()->getSegment(
            $this->request->getUri()->getTotalSegments()
        );
        $orderModel = new OrderModel();
        $order      = $orderModel->getLastOrderWithItems($vendorId, $id);

        if (!$order) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => false, 'code' => 404, 'message' => 'No orders found.', 'data' => null]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Last order details fetched successfully.',
                'data'    => ['order' => $order]
            ]);
    }

    public function razorpayCallback()
    {
        $input = $this->getInput();
        $razorpayOrderId = $this->resolvePaymentValue($input, ['razorpay_order_id', 'order_id', 'razorpayOrderId']);
        $razorpayPaymentId = $this->resolvePaymentValue($input, ['razorpay_payment_id', 'payment_id', 'razorpayPaymentId']);
        $razorpaySignature = $this->resolvePaymentValue($input, ['razorpay_signature', 'signature', 'razorpaySignature']);
        $failureUrl = getenv('RAZORPAY_FAILED_URL');
        $isAjax = $this->isAjaxRequest();
        if (!$razorpayOrderId || !$razorpayPaymentId || !$razorpaySignature) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 400,
                        'message' => 'Payment verification faileds.',
                        'data'    => ['redirect_url' => $failureUrl],
                    ]);
            }
            return redirect()->to($failureUrl);
        }
        try {
            $api = new Api(getenv('RAZORPAY_KEY_ID'), getenv('RAZORPAY_KEY_SECRET'));
            $signatureValid = false;
            try {
                $api->utility->verifyPaymentSignature([
                    'razorpay_order_id'   => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'razorpay_signature'  => $razorpaySignature,
                ]);
                $signatureValid = true;
            } catch (SignatureVerificationError $e) {
                $signatureValid = $this->verifyRazorpaySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature);
            }
            if (!$signatureValid) {
                throw new SignatureVerificationError('Razorpay signature could not be verified.');
            }
            $orderModel = new OrderModel();
            $order = $orderModel->where('razorpay_order_id', $razorpayOrderId)->first();
            if (!$order) {
                if ($isAjax) {
                    return $this->response
                        ->setStatusCode(404)
                        ->setJSON([
                            'status'  => false,
                            'code'    => 404,
                            'message' => 'Order not found.',
                            'data'    => ['redirect_url' => $failureUrl],
                        ]);
                }
                return redirect()->to($failureUrl);
            }
            $this->saveOrderPaymentDetails((int) $order['id'], [
                'payment_status'       => 'paid',
                'payment_method'       => 'razorpay',
                'razorpay_payment_id'  => $razorpayPaymentId,
                'razorpay_signature'   => $razorpaySignature,
                'payment_reference'    => $razorpayPaymentId ?: $razorpayOrderId,
                'payment_currency'     => $order['currency'] ?? null,
                'payment_amount'       => $order['total'] ?? null,
                'payment_response'     => json_encode([
                    'razorpay_order_id'   => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'razorpay_signature'  => $razorpaySignature,
                    'verified_at'         => date('Y-m-d H:i:s'),
                ]),
                'paid_at'              => date('Y-m-d H:i:s'),
            ]);
            $cartModel = new CartModel();
            $cartModel->clearCart($order['exhibitor_id'], $order['sub_event_id']);

            $subEvent = $this->db->table('company_sub_events')
                ->select('construction_date, end_date')
                ->where('id', $order['sub_event_id'])
                ->get()
                ->getRowArray();

            if ($subEvent && date('Y-m-d') >= $subEvent['construction_date'] && date('Y-m-d') <= $subEvent['end_date']) {
                register_shutdown_function(function () use ($order) {
                    $this->sendConstructionWindowPaymentEmails($order);
                });
            }

            $successUrl = getenv('RAZORPAY_SUCCESS_URL');
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON([
                        'status'  => true,
                        'code'    => 200,
                        'message' => 'Payment verified successfully.',
                        'data'    => ['redirect_url' => $successUrl],
                    ]);
            }
            return redirect()->to($successUrl);
        } catch (SignatureVerificationError $e) {
            log_message('error', 'Razorpay payment verification failed. Order: ' . ($razorpayOrderId ?? 'unknown') . ' Payment: ' . ($razorpayPaymentId ?? 'unknown') . ' Error: ' . $e->getMessage());
            $orderModel = new OrderModel();
            if ($razorpayOrderId) {
                $order = $orderModel->where('razorpay_order_id', $razorpayOrderId)->first();
                if ($order) {
                    $this->saveOrderPaymentDetails((int) $order['id'], [
                        'payment_status' => 'failed',
                        'payment_method' => 'razorpay',
                        'payment_reference' => $razorpayPaymentId ?: $razorpayOrderId,
                        'payment_response' => json_encode([
                            'razorpay_order_id'   => $razorpayOrderId,
                            'razorpay_payment_id' => $razorpayPaymentId,
                            'razorpay_signature'  => $razorpaySignature,
                            'failed_at'           => date('Y-m-d H:i:s'),
                        ]),
                    ]);
                    $failedUrl = $order['payment_failed_url'] ?: $failureUrl;

                    if ($isAjax) {
                        return $this->response
                            ->setStatusCode(400)
                            ->setJSON([
                                'status'  => false,
                                'code'    => 400,
                                'message' => 'Payment verification failed.',
                                'data'    => ['redirect_url' => $failedUrl],
                            ]);
                    }
                    return redirect()->to($failedUrl);
                }
            }
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 400,
                        'message' => 'Payment verification failed.',
                        'data'    => ['redirect_url' => $failureUrl],
                    ]);
            }
            return redirect()->to($failureUrl);
        }
    }

    private function sendConstructionWindowPaymentEmails(array $order): void
    {
        try {
            $subEventId = $order['sub_event_id'] ?? null;
            if (!$subEventId) {
                log_message('error', '[sendConstructionWindowPaymentEmails] Missing sub_event_id on order: ' . ($order['id'] ?? 'unknown'));
                return;
            }

            $db = \Config\Database::connect();
            $subEvent = $db->table('company_sub_events')
                ->select('id, sub_event_name, construction_date, end_date')
                ->where('id', $subEventId)
                ->get()
                ->getRowArray();

            if (!$subEvent || empty($subEvent['construction_date']) || empty($subEvent['end_date'])) {
                log_message('error', '[sendConstructionWindowPaymentEmails] Missing construction window dates for sub_event_id: ' . $subEventId);
                return;
            }
            $manualsetup = $db->table('manual_setup')
                ->select('id, notification_email')
                ->where('sub_event_id', $subEventId)
                ->get()
                ->getRowArray();

            $today = strtotime(date('Y-m-d'));
            $constructionStart = strtotime($subEvent['construction_date']);
            $eventEnd = strtotime($subEvent['end_date']);
            if ($today < $constructionStart || $today > $eventEnd) {
                return;
            }
            $orderItems = $db->table('order_items as oi')
                ->join('items as i', 'i.id = oi.item_id', 'left')
                ->select('i.item_name, oi.quantity')
                ->where('oi.order_id', $order['id'])
                ->get()
                ->getResultArray();

            $vendorEmail = $order['email'] ?? null;
            $vendorName  = $order['vendor_name'] ?? $order['exhibitor_name'] ?? 'Exhibitor';
            $operationsEmail = $manualsetup['notification_email'] ?? null;
            $orderNumber = $order['order_number'] ?? $order['id'] ?? '';
            $amount      = $order['total'] ?? '';
            $currency    = $order['currency'] ?? '';
            $eventName   = $subEvent['sub_event_name'] ?? '';
            $invoicePdfPath = null;

            try {
                $items = [];
                foreach ($orderItems as $row) {
                    $items[] = [
                        'item_name' => $row['item_name'] ?? '',
                        'qty'       => $row['quantity'] ?? '',
                    ];
                }
                if (empty($items)) {
                    $items = [['item_name' => 'Additional Services', 'qty' => 1]];
                }

                $viewData = [
                    'vendor_name'    => $vendorName,
                    'vendor_gstin'   => $order['gstin'] ?? 'N/A',
                    'invoice_number' => $order['invoice_number'] ?? ('SI/PI/' . date('y') . '-' . (date('y') + 1) . '/' . ($order['id'] ?? rand(1000, 9999))),
                    'invoice_date'   => $order['invoice_date'] ?? date('d.m.Y'),
                    'event_name'     => $eventName,
                    'pan_no'         => env('COMPANY_PAN_NO', 'AABFS1981P'),
                    'items'          => $items,
                ];

                $invoiceFileName = 'proforma-invoice-' . ($order['id'] ?? time()) . '.pdf';
                $pdfBytes = PdfHelper::makeFromView(
                    'peroforma-invoice-template',
                    $viewData,
                    $invoiceFileName
                );
                $tmpDir = WRITEPATH . 'uploads/tmp';
                if (!is_dir($tmpDir)) {
                    mkdir($tmpDir, 0755, true);
                }
                $invoicePdfPath = $tmpDir . '/' . $invoiceFileName;
                file_put_contents($invoicePdfPath, $pdfBytes);
            } catch (\Throwable $e) {
                log_message('error', '[sendConstructionWindowPaymentEmails] Invoice PDF generation failed: ' . $e->getMessage());
                $invoicePdfPath = null;
            }

            if ($vendorEmail) {
                $vendorSubject = 'Payment Confirmation - ' . $eventName;
                $vendorBody = "<p>Dear {$vendorName},</p>
                    <p>Your payment for order <strong>{$orderNumber}</strong> has been successfully received.</p>
                    <p><strong>Event:</strong> {$eventName}</p>
                    <p>Please find your Proforma Invoice attached.</p>
                    <p>Thank you.</p>";
                sendEmail(
                    toEmail: $vendorEmail,
                    toName: $vendorName,
                    subject: $vendorSubject,
                    htmlBody: $vendorBody,
                    attachments: $invoicePdfPath ? [$invoicePdfPath] : []
                );
            } else {
                log_message('error', '[sendConstructionWindowPaymentEmails] Vendor email missing on order: ' . ($order['id'] ?? 'unknown'));
            }

            if ($operationsEmail) {
                $opsSubject = 'New Payment Received - ' . $eventName;
                $opsBody = "<p>A new payment has been received.</p>
        <p><strong>Order:</strong> {$orderNumber}</p>
        <p><strong>Vendor:</strong> {$vendorName} ({$vendorEmail})</p>
        <p><strong>Amount:</strong> {$currency} {$amount}</p>
        <p><strong>Event:</strong> {$eventName}</p>";

                sendEmail(
                    toEmail: $operationsEmail,
                    toName: 'Operations Team',
                    subject: $opsSubject,
                    htmlBody: $opsBody,
                    attachments: $invoicePdfPath ? [$invoicePdfPath] : []
                );
            } else {
                log_message('error', '[sendConstructionWindowPaymentEmails] OPERATIONS_TEAM_EMAIL not configured in .env');
            }

            if ($invoicePdfPath && file_exists($invoicePdfPath)) {
                unlink($invoicePdfPath);
            }
        } catch (\Throwable $e) {
            log_message('error', '[sendConstructionWindowPaymentEmails] Error: ' . $e->getMessage());
        }
    }

    public function submit_exhibitor_badge()
    {
        try {
            $jwt = $this->getJwtContext();
            $subEventId  = $jwt['subEventId'] ?? null;
            $exhibitorId = $jwt['payload']->sub ?? null;
            $exhibitorIds = $jwt['exhibitor_id'] ?? null;

            if (!$exhibitorId || !$subEventId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'code'    => 401,
                        'message' => 'Unauthorized.',
                        'data'    => null
                    ]);
            }

            $setup = $this->db->table('manual_setups')
                ->select('event_id')
                ->where('sub_event_id', $subEventId)
                ->get()
                ->getRowArray();

            $eventId = $setup['event_id'] ?? null;

            if (!$eventId) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Event not found for this sub-event.'
                    ]);
            }
            $exhibitor = $this->db->table('exhibitors')
                ->select('badge_limit')
                ->where('id', $exhibitorIds)
                ->get()
                ->getRowArray();

            if (!$exhibitor) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Exhibitor not found.'
                    ]);
            }

            $badgeLimit = (int)($exhibitor['badge_limit'] ?? 0);

            $badgeCount = $this->db->table('manual_exhibitor_badges')
                ->where('exhibitor_id', $exhibitorIds)
                ->where('event_id', $eventId)
                ->where('is_deleted', 0)
                ->countAllResults();

            if ($badgeLimit > 0 && $badgeCount >= $badgeLimit) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => "Badge limit reached. You can only generate {$badgeLimit} badge(s) for this event."
                    ]);
            }

            $rules = [
                'fname' => 'required|min_length[2]|max_length[100]',
                'lname' => 'permit_empty|max_length[100]',
                'email' => 'permit_empty|valid_email|max_length[100]',
                'mobile' => 'permit_empty|numeric|min_length[10]|max_length[15]',
                'photo' => [
                    'uploaded[photo]',
                    'ext_in[photo,jpg,jpeg,png]',
                    'mime_in[photo,image/jpeg,image/png]',
                ],
            ];

            $messages = [
                'fname' => [
                    'required'   => 'First name is required.',
                    'min_length' => 'First name must have at least 2 characters.',
                    'max_length' => 'First name cannot exceed 100 characters.',
                ],
                'lname' => [
                    'max_length' => 'Last name cannot exceed 100 characters.',
                ],
                'email' => [
                    'valid_email' => 'Please enter a valid email address.',
                    'max_length'  => 'Email cannot exceed 100 characters.',
                ],
                'mobile' => [
                    'numeric'    => 'Whatsapp number must contain digits only.',
                    'min_length' => 'Whatsapp number must be at least 10 digits.',
                    'max_length' => 'Whatsapp number cannot exceed 15 digits.',
                ],
                'photo' => [
                    'uploaded' => 'Please upload a photo for the badge.',
                    'ext_in'   => 'Photo must be JPG or PNG.',
                    'mime_in'  => 'Photo must be a valid image file.',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                $errors = $this->validator->getErrors();
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'errors'  => $errors,
                        'message' => reset($errors)
                    ]);
            }
            $post = $this->request->getPost();
            if (empty($post['email']) && empty($post['mobile'])) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Please provide either an email or Whatsapp number.'
                    ]);
            }
            $photo = $this->request->getFile('photo');
            if (!$photo->isValid() || $photo->hasMoved()) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Uploaded photo is invalid.'
                    ]);
            }

            // Single source of truth for file size validation.
            $fileSizeBytes = $photo->getSize();
            $maxSizeBytes  = 1 * 1024 * 1024; // 1 MB

            if ($fileSizeBytes > $maxSizeBytes) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Photo size must be less than 1 MB.'
                    ]);
            }

            $imageData = getimagesize($photo->getTempName());
            if ($imageData === false) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Uploaded file is not a valid image.'
                    ]);
            }

            $minWidth = 300;
            $maxWidth = 1000;
            $width = $imageData[0] ?? 0;

            if ($width < $minWidth || $width > $maxWidth) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => "Photo width must be between {$minWidth}px and {$maxWidth}px. Uploaded image is {$width}px wide."
                    ]);
            }

            $photoUrl = UploadHelper::upload($photo, 'exhibitors_images', true, 'exhibitor_image');

            if (!$photoUrl) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Unable to upload photo.'
                    ]);
            }

            $saveData = [
                'event_id'       => $eventId,
                'sub_event_id'   => $subEventId,
                'exhibitor_id'   => $exhibitorIds,
                'salutation'     => $post['salutation'] ?? '',
                'first_name'     => trim($post['fname']),
                'last_name'      => trim($post['lname'] ?? ''),
                'email'          => !empty($post['email']) ? trim($post['email']) : '',
                'country_code'   => $post['country_code'] ?? null,
                'mobile_number'  => !empty($post['mobile']) ? trim($post['mobile']) : '',
                'exhibitor_image' => $photoUrl,
                'is_deleted'     => 0,
                'ex_created_at'  => date('Y-m-d H:i:s'),
                'ex_created_by'  => $exhibitorId
            ];

            $inserted = $this->db
                ->table('manual_exhibitor_badges')
                ->insert($saveData);

            if (!$inserted) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Unable to save badge.'
                    ]);
            }

            $insertId = $this->db->insertID();
            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'message' => 'Badge created successfully.',
                'data' => [
                    'id' => $insertId,
                    'encrypted_id' => encryptData($insertId)
                ]
            ]);
        } catch (\Throwable $e) {
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

    public function list()
    {

        try {
            $authHeader = $this->request->getHeaderLine('Authorization');
            if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Token missing.'
                    ]);
            }
            $payload = \App\Libraries\JwtPayload::get();
            $exhibitorId = $payload->sub ?? null;
            $exhibitorId = $payload->exhibitor_id ?? null;
            $subEventId = $payload->sub_event_id ?? null;
            if (!$exhibitorId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Invalid token.'
                    ]);
            }

            // --- NEW: badge limit lookup ---
            $exhibitorRow = $this->db->table('exhibitors')
                ->select('badge_limit')
                ->where('id', $exhibitorId)
                ->get()
                ->getRowArray();
            $badgeLimit = (int) ($exhibitorRow['badge_limit'] ?? 0);
            // --- END NEW ---

            $manualSetup = $this->db->table('manual_setups')
                ->select('online_forms_enable_disable, online_forms_open_close, manual_badges_note')
                ->where('sub_event_id', $subEventId)
                ->where('is_deleted', 0)
                ->get()
                ->getRowArray();
            $enableDisable = [];
            $openClose = [];
            $badgesNote = '';
            $exhibitorBadgeColor = '';
            $vendorBadgeColor = '';
            $exhibitorBadgeBackground = '';
            $vendorBadgeBackground = '';
            if ($manualSetup) {
                $enableDisable = !empty($manualSetup['online_forms_enable_disable'])
                    ? json_decode($manualSetup['online_forms_enable_disable'], true)
                    : [];
                $openClose = !empty($manualSetup['online_forms_open_close'])
                    ? json_decode($manualSetup['online_forms_open_close'], true)
                    : [];
                $badgesNote = $manualSetup['manual_badges_note'] ?? '';
                
                
            }
            $badgesEnabled = isset($enableDisable['exhibitor_badges']) ? (int) $enableDisable['exhibitor_badges'] : 1;
            $badgesOpen = isset($openClose['exhibitor_badges']) ? (int) $openClose['exhibitor_badges'] : 1;
            $badges = $this->db->table('manual_exhibitor_badges')
                ->select('id, salutation, first_name, last_name, email, country_code, mobile_number, exhibitor_image')
                ->where('exhibitor_id', $exhibitorId)
                ->where('is_deleted', 0)
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

            $uploadBaseUrl = rtrim(env('UPLOAD_BASE_URL', ''), '/');

            $data = [];
            foreach ($badges as $row) {
                $photoUrl = '';
                if (!empty($row['exhibitor_image'])) {
                    if (filter_var($row['exhibitor_image'], FILTER_VALIDATE_URL)) {
                        $photoUrl = $row['exhibitor_image'];
                    } else {
                        $photoUrl = $uploadBaseUrl . '/' . ltrim($row['exhibitor_image'], '/');
                    }
                }
                $data[] = [
                    'encrypted_id'  => encryptData($row['id']),
                    'salutation'    => $row['salutation'] ?? '',
                    'fname'         => $row['first_name'] ?? '',
                    'lname'         => $row['last_name'] ?? '',
                    'full_name'     => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                    'email'         => $row['email'] ?? '',
                    'country_code'  => $row['country_code'] ?? '',
                    'mobile'        => $row['mobile_number'] ?? '',
                    'photo_url'     => $photoUrl,
                ];
            }

            // --- NEW: badge summary block ---
            $badgesCreated = count($data);
            $badgesLeft = $badgeLimit > 0 ? max(0, $badgeLimit - $badgesCreated) : null; // null = unlimited
            // --- END NEW ---

            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'message' => 'Badges fetched successfully.',
                'data'    => $data,
                'badge_summary' => [ // --- NEW ---
                    'badge_limit'    => $badgeLimit,
                    'badges_created' => $badgesCreated,
                    'badges_left'    => $badgesLeft,
                    'is_unlimited'   => $badgeLimit <= 0,
                ],
                'manual_setup' => [
                    'enable_disable' => $enableDisable,
                    'open_close' => $openClose,
                    'badges_enabled' => ($badgesEnabled === 1),
                    'badges_open' => ($badgesOpen === 1),
                    'badges_note' => $badgesNote,
                   
                    'vendor_badge_color' => $vendorBadgeColor,
                    'exhibitor_badge_background' => $exhibitorBadgeBackground,
                    'vendor_badge_background' => $vendorBadgeBackground,
                    'form_status' => $this->getFormStatus($badgesEnabled, $badgesOpen)
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'exhibitor badge list failed: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'success' => false,
                    'message' => 'Something went wrong while fetching badges.',
                    'debug' => $e->getMessage()
                ]);
        }
    }

    private function getFormStatus($enabled, $open)
    {
        $enabled = (int) $enabled;
        $open    = (int) $open;

        // A closed form is hidden and cannot be accessed.
        if ($open === 0) {
            return 'disabled';
        }

        // An enabled, open form can be edited; a disabled, open form is view-only.
        if ($enabled === 1) {
            return 'enabled_open';
        }

        return 'enabled_closed';
    }

    public function update_badge($encryptedId = null)
    {
        try {
            $jwt = $this->getJwtContext();
            $subEventId  = $jwt['subEventId'] ?? null;
            $exhibitorId = $jwt['payload']->exhibitor_id ?? null;
            if (!$exhibitorId || !$subEventId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'code'    => 401,
                        'message' => 'Unauthorized.',
                        'data'    => null
                    ]);
            }
            if (empty($encryptedId)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Badge ID is required.'
                    ]);
            }
            $decrypted = decryptData($encryptedId);
            if ($decrypted === false || $decrypted === null) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Invalid badge reference.'
                    ]);
            }
            $badgeId = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $badgeId = $decrypted;
            }
            if (!is_numeric($badgeId)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Invalid badge reference.'
                    ]);
            }
            $badgeId = (int) $badgeId;
            $existing = $this->db->table('manual_exhibitor_badges')
                ->where('id', $badgeId)
                ->where('exhibitor_id', $exhibitorId)
                ->where('is_deleted', 0)
                ->get()
                ->getRowArray();
            if (!$existing) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Badge not found.'
                    ]);
            }
            $rules = [
                'fname'  => 'required|min_length[2]|max_length[100]',
                'lname'  => 'permit_empty|max_length[100]',
                'email'  => 'permit_empty|valid_email|max_length[100]',
                'mobile' => 'permit_empty|numeric|min_length[10]|max_length[15]',
            ];
            $messages = [
                'fname' => [
                    'required'   => 'First name is required.',
                    'min_length' => 'First name must have at least 2 characters.',
                    'max_length' => 'First name cannot exceed 100 characters.',
                ],
                'lname' => [
                    'max_length' => 'Last name cannot exceed 100 characters.',
                ],
                'email' => [
                    'valid_email' => 'Please enter a valid email address.',
                    'max_length'  => 'Email cannot exceed 100 characters.',
                ],
                'mobile' => [
                    'numeric'    => 'Whatsapp number must contain digits only.',
                    'min_length' => 'Whatsapp number must be at least 10 digits.',
                    'max_length' => 'Whatsapp number cannot exceed 15 digits.',
                ],
            ];
            $photo = $this->request->getFile('photo');
            if ($photo && $photo->isValid() && !$photo->hasMoved()) {
                $rules['photo'] = [
                    'max_size[photo,1024]',
                    'ext_in[photo,jpg,jpeg,png]',
                    'mime_in[photo,image/jpeg,image/png]',
                ];
                $messages['photo'] = [
                    'max_size' => 'Photo must be smaller than 1 MB.',
                    'ext_in'   => 'Photo must be JPG or PNG.',
                    'mime_in'  => 'Photo must be a valid image file.',
                ];
            }
            if (!$this->validate($rules, $messages)) {
                $errors = $this->validator->getErrors();
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'errors'  => $errors,
                        'message' => reset($errors)
                    ]);
            }
            $post = $this->request->getPost();
            if (empty($post['email']) && empty($post['mobile'])) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Please provide either an email or Whatsapp number.'
                    ]);
            }
            $updateData = [
                'salutation'     => $post['salutation'] ?? '',
                'first_name'     => trim($post['fname']),
                'last_name'      => trim($post['lname'] ?? ''),
                'email'          => !empty($post['email']) ? trim($post['email']) : '',
                'country_code'   => $post['country_code'] ?? null,
                'mobile_number'  => !empty($post['mobile']) ? trim($post['mobile']) : '',
                'ex_updated_at'  => date('Y-m-d H:i:s'),
                'ex_updated_by'  => $exhibitorId,
            ];
            if ($photo && $photo->isValid() && !$photo->hasMoved()) {
                $imageData = getimagesize($photo->getTempName());
                if ($imageData === false) {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => false,
                            'success' => false,
                            'message' => 'Uploaded file is not a valid image.'
                        ]);
                }
                $width = $imageData[0] ?? 0;
                if ($width < 300 || $width > 1000) {
                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'status'  => false,
                            'success' => false,
                            'message' => 'Photo width must be between 300px and 1000px.'
                        ]);
                }
                $newPhoto = UploadHelper::upload(
                    $photo,
                    'exhibitors_images',
                    true,
                    'exhibitor_image'
                );
                if (!$newPhoto) {
                    return $this->response
                        ->setStatusCode(500)
                        ->setJSON([
                            'status'  => false,
                            'success' => false,
                            'message' => 'Unable to upload photo.'
                        ]);
                }
                if (!empty($existing['exhibitor_image'])) {
                    try {
                        UploadHelper::delete(
                            $existing['exhibitor_image'],
                            'exhibitors_images'
                        );
                    } catch (\Throwable $e) {
                        log_message(
                            'error',
                            'Failed to delete old image: ' . $e->getMessage()
                        );
                    }
                }
                $updateData['exhibitor_image'] = $newPhoto;
            }
            $updated = $this->db->table('manual_exhibitor_badges')
                ->where('id', $badgeId)
                ->update($updateData);
            if ($updated === false) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Unable to update badge.'
                    ]);
            }

            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'message' => 'Badge updated successfully.',
                'data'    => [
                    'id'           => $badgeId,
                    'encrypted_id' => encryptData($badgeId)
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'update_exhibitor_badge failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'success' => false,
                    'message' => 'Something went wrong while updating badge.'
                ]);
        }
    }

    public function download_order_invoice($encryptedOrderId = null)
    {
        try {
            $jwt = $this->getJwtContext();
            $vendorId = $jwt['exhibitor_id'] ?? null;
            $subEventId = $jwt['subEventId'] ?? null;
            if (!$vendorId || !$subEventId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 401,
                        'message' => 'Unauthorized.',
                        'data'    => null
                    ]);
            }
            if (empty($encryptedOrderId)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 400,
                        'message' => 'Order ID is required.',
                        'data'    => null
                    ]);
            }
            $decrypted = decryptData($encryptedOrderId);
            if ($decrypted === false || $decrypted === null) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 400,
                        'message' => 'Invalid order reference.',
                        'data'    => null
                    ]);
            }
            $orderId = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $orderId = $decrypted;
            }
            if (!is_numeric($orderId)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 400,
                        'message' => 'Invalid order reference.',
                        'data'    => null
                    ]);
            }
            $orderId = (int) $orderId;
            $order = $this->db->table('orders')
                ->where('id', $orderId)
                ->where('exhibitor_id', $vendorId)
                ->get()
                ->getRowArray();
            if (!$order) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 404,
                        'message' => 'Order not found.',
                        'data'    => null
                    ]);
            }
            $eligibleStatuses = ['paid', 'completed', 'success'];
            $paymentStatus = strtolower($order['payment_status'] ?? '');
            if (!in_array($paymentStatus, $eligibleStatuses, true)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 422,
                        'message' => 'Invoice is not available until payment is confirmed.',
                        'data'    => null
                    ]);
            }
            $isInternational = $this->resolveIsInternational($vendorId);
            $currencySymbol  = $order['currency'] === 'USD' ? '$' : ($isInternational ? '$' : '₹');
            $currencyText    = $order['currency'] ?? ($isInternational ? 'USD' : 'INR');
            $contactModel = new ExhibitorContactPersonModel();
            $profile = $contactModel->getProfile($vendorId);
            if (!is_array($profile)) {
                $profile = [];
            }
            $orderItems = $this->db->table('order_items')
                ->where('order_id', $orderId)
                ->get()
                ->getResultArray();
            $subEvent = $this->db->table('manual_setups')
                ->where('sub_event_id', $subEventId)
                ->get()
                ->getRow();
            $subEvents = $this->db->table('company_sub_events')
                ->where('id', $subEventId)
                ->get()
                ->getRow();
            $exhibitorInfo = $this->getExhibitorTaxInfo($vendorId);
            $taxAmount = is_numeric($order['tax'] ?? null) ? (float) $order['tax'] : 0.0;
            $gstBreakdown = $this->resolveGstBreakdown(
                $taxAmount,
                $exhibitorInfo['state_name'] ?? null,
                $subEvents->venue_state ?? null
            );
            $cgst = $gstBreakdown['cgst'];
            $sgst = $gstBreakdown['sgst'];
            $igst = $gstBreakdown['igst'];
            $isSameState = $gstBreakdown['is_same_state'];
            $eventName = '';
            if (!empty($subEvents->sub_event_name)) {
                $eventName = $subEvents->sub_event_name;
            }
            $invoiceDate = !empty($order['created_at']) && strtotime($order['created_at']) !== false
                ? date('d.m.Y', strtotime($order['created_at']))
                : date('d.m.Y');
            $companyInfo = $this->db->table('companies')
                ->select('company_name, company_logo')
                ->where('id', 1)
                ->get()
                ->getRowArray();
            $invoiceData = [
                'invoice_no'        => $order['order_number'] ?? ('ORD-' . $orderId),
                'signature'         => $subEvents->signature ?? '',
                'date'              => $invoiceDate,
                'profile'           => $profile,
                'items'             => $orderItems,
                'subtotal'          => $order['subtotal'] ?? 0,
                'cgst'              => $cgst,
                'sgst'              => $sgst,
                'igst'              => $igst,
                'is_same_state'     => $isSameState,
                'total'             => $order['total'] ?? 0,
                'currency_symbol'   => $currencySymbol,
                'currency_text'     => $currencyText,
                'event_name'        => $eventName,
                'company_name'      => $companyInfo['company_name'] ?? '',
                'company_image'     => $companyInfo['company_logo'] ?? '',
                'customer_name'     => $exhibitorInfo['organisation_name'] ?? 'M/s Services International',
                'customer_gstin'    => $exhibitorInfo['gst_number'] ?? 'N/A',
                'customer_address'  => $exhibitorInfo['address'] ?? '',
                'payment_method'    => $order['payment_method'] ?? '',
                'payment_reference' => $order['payment_reference'] ?? '',
                'exhibitor_type'    => $exhibitorInfo['exhibitor_type'] ?? '',
            ];
            $html = $this->quotationInvoiceHtml2($invoiceData);
            if (empty($html)) {
                log_message('error', 'download_order_invoice: quotationInvoiceHtml2 returned empty HTML for order ' . $orderId);
                throw new \RuntimeException('Invoice template returned no content.');
            }
            $tempDir = WRITEPATH . 'mpdf';
            if (!is_dir($tempDir)) {
                if (!mkdir($tempDir, 0775, true) && !is_dir($tempDir)) {
                    log_message('error', 'download_order_invoice: failed to create mpdf temp dir at ' . $tempDir);
                    throw new \RuntimeException('Unable to prepare PDF working directory.');
                }
            }
            if (!is_writable($tempDir)) {
                log_message('error', 'download_order_invoice: mpdf temp dir not writable at ' . $tempDir);
                throw new \RuntimeException('PDF working directory is not writable.');
            }
            $mpdf = new Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 10,
                'margin_bottom' => 10,
                'default_font'  => 'dejavusans',
                'tempDir'       => $tempDir,
            ]);
            $mpdf->WriteHTML($html);
            $fileName = 'Invoice-' . str_replace('/', '-', (string) $invoiceData['invoice_no']) . '.pdf';
            $pdfContent = $mpdf->Output($fileName, Destination::STRING_RETURN);
            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->setHeader('Content-Length', (string) strlen($pdfContent))
                ->setBody($pdfContent);
        } catch (\Throwable $e) {
            log_message('error', 'download_order_invoice failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'code'    => 500,
                    'message' => 'Something went wrong while generating invoice PDF.',
                    'error'   => $e->getMessage(),
                    'data'    => null
                ]);
        }
    }

    public function download_exhibitor_badge($encryptedId = null)
    {
        try {
            $jwt = $this->getJwtContext();
            $vendorId = $jwt['exhibitor_id'] ?? null;
            // print_r($vendorId); die;
            $subEventId = $jwt['subEventId'] ?? null;
            if (!$vendorId || !$subEventId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'code'    => 401,
                        'message' => 'Unauthorized.',
                        'data'    => null
                    ]);
            }

            if (empty($encryptedId)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => false,
                    'code' => 400,
                    'message' => 'Badge ID is required.',
                    'data' => null
                ]);
            }

            $decrypted = decryptData($encryptedId);
            $decodedId = ($decrypted !== false && $decrypted !== null) ? json_decode($decrypted, true) : null;

            $badgeId = null;
            if (is_numeric($decodedId)) {
                $badgeId = (int) $decodedId;
            } elseif (is_numeric($decrypted)) {
                $badgeId = (int) $decrypted;
            }
            if (!$badgeId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => false,
                    'code' => 400,
                    'message' => 'Invalid badge reference.',
                    'data' => null
                ]);
            }

            $badgeModel = new ExhibitorBadgeModel();
            $fallbackTemplatePath = FCPATH . 'assets/images/default-badge-template.jpg';

            $pdf = $badgeModel->generateBadgePdf($badgeId, $vendorId, $subEventId, $fallbackTemplatePath);

            if (!$pdf) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Badge not found.',
                    'data' => null
                ]);
            }

            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $pdf['fileName'] . '"')
                ->setHeader('Content-Length', (string) strlen($pdf['content']))
                ->setBody($pdf['content']);
        } catch (\Throwable $e) {
            log_message('error', 'download_exhibitor_badge failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong while generating badge PDF.',
                'error' => $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function delete_exhibitor_badge($encryptedId = null)
    {
        try {
            $jwt = $this->getJwtContext();
            $exhibitorId = $jwt['payload']->sub ?? null;
            if (!$exhibitorId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 401,
                        'message' => 'Unauthorized.',
                        'data' => null
                    ]);
            }
            if (!$encryptedId) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 422,
                        'message' => 'Badge ID is required.',
                        'data' => null
                    ]);
            }
            $encryptedId = urldecode($encryptedId);
            $badgeId = decryptData($encryptedId);
            if (!$badgeId || !is_numeric($badgeId)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 422,
                        'message' => 'Invalid badge ID.',
                        'data' => null
                    ]);
            }
            $badgeId = (int) $badgeId;
            $badge = $this->db
                ->table('manual_exhibitor_badges')
                ->where('id', $badgeId)
                ->where('exhibitor_id', $exhibitorId)
                ->where('is_deleted', 0)
                ->get()
                ->getRowArray();
            if (!$badge) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 404,
                        'message' => 'Badge not found.',
                        'data' => null
                    ]);
            }
            $updated = $this->db
                ->table('manual_exhibitor_badges')
                ->where('id', $badgeId)
                ->where('exhibitor_id', $exhibitorId)
                ->update([
                    'is_deleted' => 1,
                    'ex_deleted_by' => $exhibitorId,
                    'ex_deleted_at' => date('Y-m-d H:i:s')
                ]);
            if (!$updated) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['status' => false, 'success' => false, 'code' => 500, 'message' => 'Unable to delete badge.', 'data' => null]);
            }
            return $this->response
                ->setStatusCode(200)
                ->setJSON(['status' => true, 'success' => true, 'code' => 200, 'message' => 'Badge deleted successfully.', 'data' => null]);
        } catch (\Throwable $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status' => false,
                    'success' => false,
                    'code' => 500,
                    'message' => 'Something went wrong while deleting badge.',
                    'error' => $e->getMessage(),
                    'data' => null
                ]);
        }
    }

    public function getGuidelines()
    {
        $payload = JwtPayload::get();
        $subEventId = $payload->sub_event_id ?? null;
        $exhibitorId = $payload->sub ?? null;
        if (!$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }
        $fasciaCategory = null;
        if ($exhibitorId) {
            $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
                ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
                ->join('stall_types as st', 'e.stall_type_id = st.id', 'left')
                ->select('st.stall_type as fascia_category')
                ->where('ecp.id', $exhibitorId)
                ->where('e.sub_event_id', $subEventId)
                ->get()->getRowArray();
            $fasciaCategory = $exhibitor['fascia_category'] ?? null;
        }
        $isRawSpace = ((int) $fasciaCategory === 1);
        $builder = $this->db->table('manual_pages_menu m');
        $builder->select("m.id as menu_id, m.menu_name, p.id as page_id, p.page_title, p.page_url, p.serial_no");
        $builder->join('manual_pages p', 'p.menu_id = m.id AND p.is_deleted = 0', 'left');
        $builder->where('m.is_deleted', 0);
        $builder->where('m.sub_event_id', $subEventId);
        $builder->orderBy('p.serial_no', 'ASC');
        $result = $builder->get()->getResultArray();
        $menus = [];
        foreach ($result as $row) {
            $menuId = $row['menu_id'];
            $menuName = $row['menu_name'];
            if (strcasecmp(trim($menuName), 'Fabricated') === 0 && !$isRawSpace) {
                continue;
            }
            if (!isset($menus[$menuId])) {
                $menus[$menuId] = ['menu_id' => $menuId, 'menu_name' => $menuName, 'pages' => []];
            }
            if (!empty($row['page_id'])) {
                $menus[$menuId]['pages'][] = [
                    'page_id' => $row['page_id'],
                    'page_title' => $row['page_title'],
                    'page_url' => $row['page_url'],
                    'serial_no' => $row['serial_no'],
                ];
            }
        }
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Guidelines fetched successfully.',
            'data' => ['menus' => array_values($menus)],
        ]);
    }

    public function getFasicaMenu()
    {
        $payload = JwtPayload::get();
        $subEventId = $payload->sub_event_id ?? null;
        $exhibitorId = $payload->sub ?? null;
        if (!$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }
        if (!$exhibitorId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }
        $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
            ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
            ->select('e.stall_type_id as fascia_category')
            ->where('ecp.id', $exhibitorId)
            ->where('e.sub_event_id', $subEventId)
            ->get()->getRowArray();
        if (!$exhibitor) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'code' => 404,
                'message' => 'Exhibitor not found.',
                'data' => null,
            ]);
        }
        $fasciaCategory = $exhibitor['fascia_category'] ?? null;
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Fascia category fetched successfully.',
            'data' => [
                'fascia_category' => (int) $fasciaCategory,
            ],
        ]);
    }

    public function getPageContent()
    {
        $payload = JwtPayload::get();
        $subEventId = $payload->sub_event_id ?? null;
        $exhibitorId = $payload->sub ?? null;
        if (!$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }
        $uri = service('uri');
        $segments = $uri->getSegments();
        $pageUrl = end($segments);
        if (!$pageUrl) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'code' => 400,
                'message' => 'Page URL is missing.',
                'data' => null,
            ]);
        }
        $fasciaCategory = null;
        if ($exhibitorId) {
            $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
                ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
                ->join('stall_types as st', 'e.stall_type_id = st.id', 'left')
                ->select('st.stall_type as fascia_category')
                ->where('ecp.id', $exhibitorId)
                ->where('e.sub_event_id', $subEventId)
                ->get()->getRowArray();
            $fasciaCategory = $exhibitor['fascia_category'] ?? null;
        }

        $isRawSpace = ((int) $fasciaCategory === 1);
        $page = $this->db->table('manual_pages p')
            ->select('p.id as page_id, p.page_title, p.page_content, m.menu_name')
            ->join('manual_pages_menu m', 'm.id = p.menu_id', 'left')
            ->where('p.page_url', $pageUrl)
            ->where('p.sub_event_id', $subEventId)
            ->where('p.is_deleted', 0)
            ->where('m.is_deleted', 0)
            ->get()->getRowArray();
        if (!$page) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'code' => 404,
                'message' => 'Page not found.',
                'data' => null,
            ]);
        }
        if (strcasecmp(trim($page['menu_name'] ?? ''), 'Fabricated') === 0 && !$isRawSpace) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => false,
                'code' => 403,
                'message' => 'You do not have access to this page.',
                'data' => null,
            ]);
        }
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Content fetched successfully.',
            'data' => [
                'page_id' => $page['page_id'],
                'page_title' => $page['page_title'],
                'page_content' => $page['page_content'],
            ],
        ]);
    }

    public function getCasualGstDetails()
    {
        $payload = JwtPayload::get();
        $subEventId = $payload->sub_event_id ?? null;
        $exhibitorId = $payload->sub ?? null;
        if (!$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }

        $eventDetails = $this->db->table('company_sub_events se')
            ->join('company_events e', 'e.id = se.event_id', 'left')
            ->select('se.id as sub_event_id, e.event_name, se.venue_city, se.venue_state, se.venue')
            ->where('se.id', $subEventId)
            ->get()->getRowArray();

        if (!$eventDetails) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'code' => 404,
                'message' => 'Event not found.',
                'data' => null,
            ]);
        }
        $exhibitor = null;
        if ($exhibitorId) {
            $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
                ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
                ->join('cities as c', 'c.id = e.city_id', 'left')
                ->select('e.id as exhibitor_id, c.name as city_name, e.organisation_name as casual_trade_name, e.casual_gst_number, e.casual_gst_document as casual_gst_certificate')
                ->where('ecp.id', $exhibitorId)
                ->where('e.sub_event_id', $subEventId)
                ->get()->getRowArray();
        }
        $exhibitorCity = $exhibitor['city_name'] ?? null;
        $isSameCity = false;
        if ($exhibitorCity && !empty($eventDetails['venue_city'])) {
            $isSameCity = (strcasecmp(trim($exhibitorCity), trim($eventDetails['venue_city'])) === 0);
        }
        $savedGst = $exhibitor;
        $isSubmitted = !empty($savedGst) && !empty($savedGst['casual_gst_number']);
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Casual GST details fetched successfully.',
            'data' => [
                'event_details' => [
                    'event_name'  => $eventDetails['event_name'],
                    'venue_city'  => $eventDetails['venue_city'],
                    'venue_state' => $eventDetails['venue_state'],
                    'venue'       => $eventDetails['venue'],
                ],
                'is_same_city' => $isSameCity,
                'is_submitted' => $isSubmitted,
                'saved_gst'    => $savedGst ?: null,
            ],
        ]);
    }

    public function submitCasualGst()
    {
        $payload = JwtPayload::get();
        $subEventId  = $payload->sub_event_id ?? null;
        $exhibitorId = $payload->sub ?? null;
        if (!$subEventId || !$exhibitorId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'code'    => 401,
                'message' => 'Unauthorized.',
                'data'    => null,
            ]);
        }
        $tradeName = trim($this->request->getPost('casual_trade_name') ?? '');
        $gstNumber = trim($this->request->getPost('casual_gst_number') ?? '');
        if (!$tradeName || !$gstNumber) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'code'    => 422,
                'message' => 'Trade name and GST number are required.',
                'data'    => null,
            ]);
        }
        $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
            ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
            ->select('e.id as exhibitor_id, e.casual_gst_document')
            ->where('ecp.id', $exhibitorId)
            ->where('e.sub_event_id', $subEventId)
            ->get()
            ->getRowArray();
        if (!$exhibitor) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'code'    => 404,
                'message' => 'Exhibitor not found.',
                'data'    => null,
            ]);
        }
        $data = [
            'organisation_name' => $tradeName,
            'casual_gst_number' => $gstNumber,
        ];
        $certificateUrl = $exhibitor['casual_gst_document'] ?? null;
        $file = $this->request->getFile('casual_gst_certificate');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, $allowedExtensions)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => false,
                    'message' => 'Only PDF, JPG, JPEG and PNG files are allowed.',
                ]);
            }
            if ($file->getSize() > (2 * 1024 * 1024)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => false,
                    'message' => 'Maximum file size is 2 MB.',
                ]);
            }
            $uploadedFile = UploadHelper::upload(
                $file,
                'exhibitors_casual_gst_documents',
                true,
                'casual_gst_document'
            );
            if (!$uploadedFile) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => false,
                    'message' => 'Unable to upload GST document.',
                ]);
            }
            if ($exhibitor['casual_gst_document']) {
                UploadHelper::delete(
                    $exhibitor['casual_gst_document'],
                    'exhibitors_casual_gst_documents',
                );
            }
            $certificateUrl = $uploadedFile;
            $data['casual_gst_document'] = $uploadedFile;
        }

        $this->db->table('exhibitors')
            ->where('id', $exhibitor['exhibitor_id'])
            ->update($data);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => true,
            'code'    => 200,
            'message' => 'Casual GST details submitted successfully.',
            'data'    => [
                'casual_trade_name'      => $tradeName,
                'casual_gst_number'      => $gstNumber,
                'casual_gst_certificate' => $certificateUrl,
            ],
        ]);
    }

    public function getCountries()
    {
        $countries = $this->db->table('countries')
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Countries fetched successfully.',
            'data' => $countries,
        ]);
    }

    public function getStates()
    {
        $countryId = $this->request->getGet('country_id');
        if (!$countryId) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'code' => 422,
                'message' => 'country_id is required.',
                'data' => null,
            ]);
        }
        $states = $this->db->table('states')
            ->select('id, name')
            ->where('country_id', $countryId)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'States fetched successfully.',
            'data' => $states,
        ]);
    }

    public function getCities()
    {
        $stateId = $this->request->getGet('state_id');
        if (!$stateId) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'code' => 422,
                'message' => 'state_id is required.',
                'data' => null,
            ]);
        }
        $cities = $this->db->table('cities')
            ->select('id, name')
            ->where('state_id', $stateId)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Cities fetched successfully.',
            'data' => $cities,
        ]);
    }

    public function submitVisitorTicketRequest()
    {
        $payload = JwtPayload::get();
        $subEventId = $payload->sub_event_id ?? null;
        $exhibitorId = $payload->exhibitor_id ?? null;
        $contactpersonId = $payload->sub ?? null;
        $eventId = $payload->event_id ?? null;
        if (empty($eventId) && !empty($subEventId)) {
            $eventId = $this->resolveEventIdFromSubEvent($subEventId);
        }
        if (!$subEventId || !$exhibitorId || !$eventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }
        $quantity = (int) $this->request->getPost('quantity');
        $address  = trim($this->request->getPost('address') ?? '');
        $countryId = $this->request->getPost('country');
        $stateId   = $this->request->getPost('state');
        $cityId    = $this->request->getPost('city');
        $pincode   = trim($this->request->getPost('pincode') ?? '');
        $errors = [];
        if ($quantity < 1 || $quantity > 2000) {
            $errors['quantity'] = 'Quantity must be between 1 and 2000.';
        }
        if (!$address) {
            $errors['address'] = 'Address is required.';
        }
        if (!$countryId) {
            $errors['country'] = 'Country is required.';
        }
        if (!$stateId) {
            $errors['state'] = 'State is required.';
        }
        if (!$cityId) {
            $errors['city'] = 'City is required.';
        }
        if (!preg_match('/^[0-9]{6}$/', $pincode)) {
            $errors['pincode'] = 'Enter a valid 6-digit pincode.';
        }
        if (!empty($errors)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'code' => 422,
                'message' => 'Validation failed.',
                'data' => $errors,
            ]);
        }
        $this->db->table('visitor_ticket_requests')->insert([
            'exhibitor_id' => $exhibitorId,
            'event_id'     => $eventId,
            'sub_event_id' => $subEventId,
            'quantity'     => $quantity,
            'address'      => $address,
            'country_id'   => $countryId,
            'state_id'     => $stateId,
            'city_id'      => $cityId,
            'pincode'      => $pincode,
            'ex_created_by'      => $contactpersonId,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Ticket request submitted successfully.',
            'data' => null,
        ]);
    }

    public function updateVisitorTicketRequest()
    {
        $payload = JwtPayload::get();
        $subEventId = $payload->sub_event_id ?? null;
        $exhibitorId = $payload->exhibitor_id ?? null;
        $eventId = $payload->event_id ?? null;
        $contactpersonId = $payload->sub ?? null;

        if (empty($eventId) && !empty($subEventId)) {
            $eventId = $this->resolveEventIdFromSubEvent($subEventId);
        }

        if (!$subEventId || !$exhibitorId || !$eventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }

        // Get POST data
        $requestId = (int) $this->request->getPost('request_id');
        $quantity = (int) $this->request->getPost('quantity');
        $address  = trim($this->request->getPost('address') ?? '');
        $countryId = (int) $this->request->getPost('country');
        $stateId   = (int) $this->request->getPost('state');
        $cityId    = (int) $this->request->getPost('city');
        $pincode   = trim($this->request->getPost('pincode') ?? '');

        // Validation
        $errors = [];

        if (!$requestId || $requestId <= 0) {
            $errors['request_id'] = 'Valid Request ID is required.';
        }

        if ($quantity < 1 || $quantity > 2000) {
            $errors['quantity'] = 'Quantity must be between 1 and 2000.';
        }

        if (empty($address)) {
            $errors['address'] = 'Address is required.';
        }

        if (empty($countryId) || $countryId <= 0) {
            $errors['country'] = 'Country is required.';
        }

        if (empty($stateId) || $stateId <= 0) {
            $errors['state'] = 'State is required.';
        }

        if (empty($cityId) || $cityId <= 0) {
            $errors['city'] = 'City is required.';
        }

        if (!preg_match('/^[0-9]{6}$/', $pincode)) {
            $errors['pincode'] = 'Enter a valid 6-digit pincode.';
        }

        if (!empty($errors)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'code' => 422,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ]);
        }

        // Check if request exists and belongs to this exhibitor
        $existingRequest = $this->db->table('visitor_ticket_requests')
            ->where('id', $requestId)
            ->where('exhibitor_id', $exhibitorId)
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRow();

        if (!$existingRequest) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'code' => 404,
                'message' => 'Request not found or you do not have permission to update it.',
                'data' => null,
            ]);
        }

        // Check if request is still pending
        // if (strtolower($existingRequest->status) !== 'pending') {
        //     return $this->response->setStatusCode(403)->setJSON([
        //         'status' => false,
        //         'code' => 403,
        //         'message' => 'Cannot update request. Current status is "' . $existingRequest->status . '". Only pending requests can be updated.',
        //         'data' => null,
        //     ]);
        // }

        // Update the request
        $updateData = [
            'quantity'   => $quantity,
            'address'    => $address,
            'country_id' => $countryId,
            'state_id'   => $stateId,
            'city_id'    => $cityId,
            'pincode'    => $pincode,
            'ex_updated_by' => $contactpersonId,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('visitor_ticket_requests')
            ->where('id', $requestId)
            ->update($updateData);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Ticket request updated successfully.',
            'data' => null,
        ]);
    }

    public function getVisitorTicketRequests()
    {
        $jwt = $this->getJwtContext();
        $exhibitorId = $jwt['exhibitor_id'];
        $contactpersonId = $jwt['payload']->sub ?? null;
        $subEventId = $jwt['subEventId'];
        if (!$exhibitorId || !$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }
        $requests = $this->db->table('visitor_ticket_requests vtr')
            ->join('countries co', 'co.id = vtr.country_id', 'left')
            ->join('states st', 'st.id = vtr.state_id', 'left')
            ->join('cities ci', 'ci.id = vtr.city_id', 'left')
            ->select('vtr.id, vtr.quantity, vtr.country_id, vtr.state_id, vtr.city_id, vtr.address, vtr.pincode, vtr.created_at, co.name as country_name, st.name as state_name, ci.name as city_name')
            ->where('vtr.exhibitor_id', $exhibitorId)
            ->where('vtr.sub_event_id', $subEventId)
            ->orderBy('vtr.id', 'DESC')
            ->get()->getResultArray();

        $totalQuantity = 0;
        foreach ($requests as &$row) {
            $totalQuantity += (int) $row['quantity'];
            $row['location'] = trim(implode(', ', array_filter([
                $row['city_name'] ?? null,
                $row['state_name'] ?? null,
                $row['country_name'] ?? null,
            ])));
            unset($row['country_name'], $row['state_name'], $row['city_name']);
        }
        unset($row);
        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Ticket requests fetched successfully.',
            'data' => [
                'requests' => $requests,
                'total_quantity' => $totalQuantity,
            ],
        ]);
    }
    public function download_quotation($qid = null)
{
    try {
        $jwt = $this->getJwtContext();
        $vendorId = $jwt['exhibitor_id'] ?? null;
        if (!$vendorId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null
                ]);
        }

        if (empty($qid)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status'  => false,
                    'code'    => 422,
                    'message' => 'Quotation ID is required.',
                    'data'    => null
                ]);
        }

        $quote = $this->db->table('quotes')
            ->where('qid', $qid)
            ->where('exhibitor_id', $vendorId)
            ->get()
            ->getRowArray();

        if (!$quote) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Quotation not found.',
                    'data'    => null
                ]);
        }

        $detailRows = $this->db->table('quotes_details')
            ->where('qid', $qid)
            ->get()
            ->getResultArray();

        $items = array_map(function ($row) {
            return [
                'id'            => $row['item_id'],
                'item_name'     => $row['item_name'],
                'quantity'      => (int) $row['quantity'],
                'price'         => (float) $row['unit_price'],
                'sale_price'    => (float) ($row['sale_price'] ?? $row['unit_price']),
                'is_early_bird' => (bool) ($row['is_early_bird'] ?? 0),
                'item_image'    => $row['item_image'] ?? null,
            ];
        }, $detailRows);

        $isInternational = $this->resolveIsInternational($vendorId);
        $currencySymbol  = $quote['currency'] ?: ($isInternational ? '$' : '₹');
        $currencyText    = $isInternational ? 'USD' : 'INR';

        $contactModel = new ExhibitorContactPersonModel();
        $profile      = $contactModel->getProfile($vendorId);

        $subtotal  = (float) $quote['q_amount'];
        $total     = (float) $quote['amount'];
        $taxAmount = round($total - $subtotal, 2);
        $invoiceNo = $quote['ref_no'];
        $date      = !empty($quote['added_date'])
            ? date('d.m.Y', strtotime($quote['added_date']))
            : date('d.m.Y');

        $quoteSubEventId = $quote['event_id'] ?? null;

        $subEvent = $this->db->table('manual_setups')
            ->where('sub_event_id', $quoteSubEventId)
            ->get()
            ->getRow();

        $subEvents = $this->db->table('company_sub_events')
            ->where('id', $quoteSubEventId)
            ->get()
            ->getRow();

        $exhibitorInfo = $this->getExhibitorTaxInfo($vendorId);

        $gstBreakdown = $this->resolveGstBreakdown(
            $taxAmount,
            $exhibitorInfo['name'] ?? null,
            $subEvents->venue_state ?? null,
            $isInternational
        );
        $cgst = $gstBreakdown['cgst'];
        $sgst = $gstBreakdown['sgst'];
        $igst = $gstBreakdown['igst'];
        $isSameState = $gstBreakdown['is_same_state'];

        $eventName = !empty($subEvents->sub_event_name) ? $subEvents->sub_event_name : '';

        $companyInfo = $this->db->table('companies')
            ->select('company_name, company_logo')
            ->where('id', 1)
            ->get()
            ->getRowArray();

        $invoiceData = [
            'invoice_no'       => $invoiceNo,
            'signature'        => $subEvent->signature ?? '',
            'date'             => $date,
            'profile'          => $profile,
            'items'            => $items,
            'subtotal'         => $subtotal,
            'company_name'     => $companyInfo['company_name'] ?? '',
            'company_image'    => $companyInfo['company_logo'] ?? '',
            'exhibitor_type'   => $exhibitorInfo['exhibitor_type'] ?? '',
            'cgst'             => $cgst,
            'sgst'             => $sgst,
            'igst'             => $igst,
            'is_same_state'    => $isSameState,
            'total'            => $total,
            'currency_symbol'  => $currencySymbol,
            'currency_text'    => $currencyText,
            'event_name'       => $eventName,
            'customer_name'    => $exhibitorInfo['organisation_name'] ?? 'M/s Services International',
            'customer_gstin'   => $exhibitorInfo['gst_number'] ?? 'N/A',
            'customer_address' => $exhibitorInfo['address'] ?? '',
        ];

        $html = $this->quotationInvoiceHtml($invoiceData);

        $tempDir = WRITEPATH . 'mpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 10,
            'margin_bottom' => 10,
            'default_font'  => 'dejavusans',
            'tempDir'       => $tempDir,
        ]);
        $mpdf->WriteHTML($html);
        $fileName = 'Additional-Furniture-Quotation-' . str_replace('/', '-', $invoiceNo) . '.pdf';
        $pdfContent = $mpdf->Output($fileName, Destination::STRING_RETURN);

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Content-Length', strlen($pdfContent))
            ->setBody($pdfContent);
    } catch (\Throwable $e) {
        log_message('error', 'download_quotation failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
        return $this->response
            ->setStatusCode(500)
            ->setJSON([
                'status'  => false,
                'code'    => 500,
                'message' => 'Something went wrong while generating quotation PDF.',
                'error'   => $e->getMessage(),
                'data'    => null
            ]);
    }
}

    public function getReferenceImage()
    {
        try {
            $jwt = $this->getJwtContext();
            $subEventId = $jwt['subEventId'] ?? null;
            $exhibitorId = $jwt['payload']->exhibitor_id ?? null;
            
            if (!$subEventId || !$exhibitorId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Unauthorized. Invalid or missing authentication token.',
                    'data' => null,
                ]);
            }
        
            $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
                ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
                ->select('e.stall_type_id')
                ->where('ecp.id', $exhibitorId)
                ->where('ecp.sub_event_id', $subEventId)
                ->where('ecp.is_deleted', 0)
                ->get()
                ->getRowArray();
          
            if (empty($exhibitor)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Exhibitor not found.',
                    'data' => null,
                ]);
            }
            $fasciaCategory = (int) ($exhibitor['stall_type_id'] ?? 0);
            if ($fasciaCategory == 2) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => false,
                    'code' => 403,
                    'message' => 'You do not have access to this page. This feature is only available for Shell Space exhibitors.',
                    'data' => null,
                ]);
            }
            
            $manualSetup = $this->db->table('manual_setups')
                ->select('shell_space_reference_img')
                ->where('sub_event_id', $subEventId)
                ->where('is_deleted', 0)
                ->get()
                ->getRowArray();
            
            if (empty($manualSetup) || empty($manualSetup['shell_space_reference_img'])) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Reference image not available for this event.',
                    'data' => null,
                ]);
            }
            
            $uploadBaseUrl = env('UPLOAD_BASE_URL', '');
            $imagePath = $manualSetup['shell_space_reference_img'];
            
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $imageUrl = $imagePath;
            } else {
                $imagePath = ltrim($imagePath, '/');
                $imageUrl = rtrim($uploadBaseUrl, '/') . '/' . $imagePath;
            }
            
            return $this->response->setStatusCode(200)->setJSON([
                'status' => true,
                'code' => 200,
                'message' => 'Reference image fetched successfully.',
                'data' => [
                    'image_url' => $imageUrl,
                    'image_path' => $imagePath,
                ],
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[getReferenceImage] Exception: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'code' => 500,
                'message' => 'An error occurred while fetching the reference image.',
                'data' => null,
            ]);
        }
    }
    public function getElectricityItem()
    {
        $jwt = $this->getJwtContext();
        $vendorId    = $jwt['vendorId'];
        $subEventId  = $jwt['subEventId'];
        $exhibitorId = $jwt['exhibitor_id'];

        if (!$vendorId || !$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'code'    => 401,
                'message' => 'Unauthorized.',
                'data'    => null,
            ]);
        }

        // Same resolver used in additional_furniture()
        $resolved = $this->newresolveIsInternational($exhibitorId, $subEventId);
        $isInternational = $resolved['is_international'];
        $currencySymbol  = $isInternational ? '$' : '₹';

        try {
            $item = $this->db->table('items')
                ->select('id, item_name, item_image, early_bird_date, early_bird_price_inr, early_bird_price_usd, sale_price_inr, sale_price_usd, description, is_electricity')
                ->where('sub_event_id', $subEventId)
                ->where('is_electricity', 1)
                ->where('is_deleted', 0)
                ->get()
                ->getRowArray();
        } catch (\Exception $e) {
            log_message('error', '[getElectricityItem] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'code'    => 500,
                'message' => 'Failed to load electricity item.',
                'data'    => null,
            ]);
        }

        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'code'    => 404,
                'message' => 'Electricity item not configured.',
                'data'    => null,
            ]);
        }

        $today       = date('Y-m-d');
        $isEarlyBird = !empty($item['early_bird_date']) && ($today <= $item['early_bird_date']);
        $salePrice   = $isInternational ? (float) $item['sale_price_usd'] : (float) $item['sale_price_inr'];
        $price       = $isEarlyBird
            ? ($isInternational ? (float) $item['early_bird_price_usd'] : (float) $item['early_bird_price_inr'])
            : $salePrice;

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => true,
            'code'    => 200,
            'message' => 'Electricity item fetched successfully.',
            'data'    => [
                'item_id'         => (int) $item['id'],
                'item_name'       => $item['item_name'],
                'item_image'      => $item['item_image'],
                'description'     => $item['description'],
                'rate_per_kw'     => $price,
                'sale_price'      => $salePrice,
                'is_early_bird'   => $isEarlyBird,
                'currency_symbol' => $currencySymbol,
            ],
        ]);
    }

    public function saveProfile()
    {
        try {
            $jwt = $this->getJwtContext();
            $exhibitorContactId = $jwt['vendorId'] ?? null;
            $subEventId = $jwt['subEventId'] ?? null;
            $ExId = $jwt['exhibitor_id'] ?? null;

            if (!$exhibitorContactId || !$subEventId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => false,
                    'success' => false,
                    'code' => 401,
                    'message' => 'Unauthorized.',
                    'data' => null,
                ]);
            }

            $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
                ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
                ->join('company_sub_events as cse', 'cse.id = e.sub_event_id', 'left')
                ->select('e.id as exhibitor_id, e.brand_logo, e.app_logo, cse.sub_event_name')
                ->where('ecp.id', $exhibitorContactId)
                ->where('e.sub_event_id', $subEventId)
                ->get()->getRowArray();

            if (!$exhibitor) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => false,
                    'success' => false,
                    'code' => 404,
                    'message' => 'Exhibitor not found.',
                    'data' => null,
                ]);
            }

            $post = $this->request->getPost();
            $errors = [];

            $companyProductSpecialization = trim($post['company_product_specialization'] ?? '');
            $companyProfile = trim($post['brand_profile'] ?? '');
            $nameOnCertificateMemento = trim($post['name_on_certificate_memento'] ?? '');

            if ($companyProductSpecialization === '') {
                $errors['company_product_specialization'] = 'This field is required.';
            } elseif (mb_strlen($companyProductSpecialization) < 20) {
                $errors['company_product_specialization'] = 'Minimum 20 characters required.';
            } elseif (mb_strlen($companyProductSpecialization) > 500) {
                $errors['company_product_specialization'] = 'Maximum 500 characters allowed.';
            }

            if ($companyProfile !== '') {
                if (mb_strlen($companyProfile) < 20) {
                    $errors['brand_profile'] = 'Minimum 20 characters required.';
                } elseif (mb_strlen($companyProfile) > 1000) {
                    $errors['brand_profile'] = 'Maximum 1000 characters allowed.';
                }
            }

            if ($nameOnCertificateMemento !== '' && mb_strlen($nameOnCertificateMemento) > 150) {
                $errors['name_on_certificate_memento'] = 'Maximum 150 characters allowed.';
            }

            $contactPersonName = trim($post['contact_person_name'] ?? '');
            $contactPersonNumber = trim($post['contact_person_number'] ?? '');
            $contactPersonEmail = trim($post['contact_person_email'] ?? '');
            $hasContactFields = ($contactPersonName !== '' || $contactPersonNumber !== '' || $contactPersonEmail !== '');

            if ($hasContactFields) {
                if ($contactPersonName === '' || mb_strlen($contactPersonName) < 3) {
                    $errors['contact_person_name'] = 'Contact name must be at least 3 characters.';
                }
                if (!preg_match('/^[0-9]{10}$/', $contactPersonNumber)) {
                    $errors['contact_person_number'] = 'Enter a valid 10-digit phone number.';
                }
                if (!filter_var($contactPersonEmail, FILTER_VALIDATE_EMAIL)) {
                    $errors['contact_person_email'] = 'Enter a valid email address.';
                }
            }

            $productDealsIn = $post['product_deals_in'] ?? [];
            if (!is_array($productDealsIn)) {
                $productDealsIn = [];
            }
            $productDealsIn = array_map('intval', $productDealsIn);
            $productDealsInOther = trim($post['product_deals_in_other'] ?? '');

            if (isset($post['product_deals_in']) && empty($productDealsIn) && $productDealsInOther === '') {
                $errors['product_deals_in'] = 'Please select at least one product category.';
            }

            if (!empty($errors)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => false,
                    'success' => false,
                    'code' => 422,
                    'message' => 'Validation failed.',
                    'errors' => $errors,
                    'data' => null,
                ]);
            }

            $data = [
                'company_product_specialization' => $companyProductSpecialization,
                'brand_profile' => $companyProfile,
                'name_on_certificate_memento' => $nameOnCertificateMemento,
                'ex_updated_by' => $exhibitorContactId,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $contactData = [];
            if ($hasContactFields) {
                $contactData = [
                    'first_name' => $contactPersonName,
                    'mobile_number' => $contactPersonNumber,
                    'email' => $contactPersonEmail,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            if ($productDealsInOther !== '') {
                $columns = $this->db->getFieldNames('exhibitors');
                if (in_array('product_deals_in_other', $columns)) {
                    $data['product_deals_in_other'] = $productDealsInOther;
                }
            }

            $brandLogo = $this->request->getFile('brand_logo');
            if ($brandLogo && $brandLogo->isValid() && !$brandLogo->hasMoved()) {
                $allowed = ['jpg', 'jpeg', 'png'];
                $ext = strtolower($brandLogo->getClientExtension());
                $maxSize = 1 * 1024 * 1024;

                if (!in_array($ext, $allowed)) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 422,
                        'message' => 'Brand logo must be a JPG or PNG image.',
                        'data' => null,
                    ]);
                }

                if ($brandLogo->getSize() > $maxSize) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 422,
                        'message' => 'Brand logo must be smaller than 1 MB.',
                        'data' => null,
                    ]);
                }

                UploadHelper::delete($exhibitor['brand_logo'], 'exhibitors_brand_logoes');
                $uploadedLogoUrl = UploadHelper::upload($brandLogo, 'exhibitors_brand_logoes', true, 'brand_logo');
                $data['brand_logo'] = $uploadedLogoUrl;
            } elseif (empty($exhibitor['brand_logo'])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => false,
                    'success' => false,
                    'code' => 422,
                    'message' => 'Company logo is required.',
                    'data' => null,
                ]);
            }

            $appLogo = $this->request->getFile('app_logo');
            if ($appLogo && $appLogo->isValid() && !$appLogo->hasMoved()) {
                $allowed = ['jpg', 'jpeg', 'png'];
                $ext = strtolower($appLogo->getClientExtension());
                $maxSize = 1 * 1024 * 1024;

                if (!in_array($ext, $allowed)) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 422,
                        'message' => 'App logo must be a JPG or PNG image.',
                        'data' => null,
                    ]);
                }

                if ($appLogo->getSize() > $maxSize) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 422,
                        'message' => 'App logo must be smaller than 1 MB.',
                        'data' => null,
                    ]);
                }

                UploadHelper::delete($exhibitor['app_logo'], 'exhibitors_app_logoes');
                $data['app_logo'] = UploadHelper::upload($appLogo, 'exhibitors_app_logoes');
            }

            $this->db->transBegin();

            try {
                $updated = $this->db->table('exhibitors')
                    ->where('id', $exhibitor['exhibitor_id'])
                    ->update($data);

                if ($updated === false) {
                    $this->db->transRollback();
                    return $this->response->setStatusCode(500)->setJSON([
                        'status' => false,
                        'success' => false,
                        'code' => 500,
                        'message' => 'Unable to save profile.',
                        'data' => null,
                    ]);
                }

                if (!empty($contactData)) {
                    $this->db->table('exhibitor_contact_persons')
                        ->where('id', $exhibitorContactId)
                        ->update($contactData);
                }

                if (isset($post['product_deals_in'])) {
                    $this->db->table('exhibitor_deal_in_products')
                        ->where('exhibitor_id', $exhibitor['exhibitor_id'])
                        ->update([
                            'is_deleted' => 1,
                            'deleted_by' => $exhibitorContactId,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    if (!empty($productDealsIn)) {
                        $insertData = [];
                        foreach ($productDealsIn as $productId) {
                            // Get the category ID for this product
                            $product = $this->db->table('products')
                                ->select('product_category_id')
                                ->where('id', $productId)
                                ->where('is_deleted', 0)
                                ->get()->getRowArray();

                            if ($product) {
                                $insertData[] = [
                                    'exhibitor_id' => $exhibitor['exhibitor_id'],
                                    'product_category_id' => $product['product_category_id'],
                                    'product_id' => $productId,
                                    'is_deleted' => 0,
                                    'created_by' => $exhibitorContactId,
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }

                        if (!empty($insertData)) {
                            $this->db->table('exhibitor_deal_in_products')
                                ->insertBatch($insertData);
                        }
                    }
                }

                $this->db->transCommit();

                return $this->response->setStatusCode(200)->setJSON([
                    'status' => true,
                    'success' => true,
                    'code' => 200,
                    'message' => 'Profile saved successfully!',
                    'data' => $data,
                ]);
            } catch (\Exception $e) {
                $this->db->transRollback();
                log_message('error', 'saveProfile transaction failed: ' . $e->getMessage());

                return $this->response->setStatusCode(500)->setJSON([
                    'status' => false,
                    'success' => false,
                    'code' => 500,
                    'message' => 'Failed to save profile: ' . $e->getMessage(),
                    'data' => null,
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'saveProfile failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'success' => false,
                'code' => 500,
                'message' => 'Something went wrong while saving profile.',
                'data' => null,
            ]);
        }
    }

    public function getproduct()
    {
        try {
            $jwt = $this->getJwtContext();

            $exhibitorContactId = $jwt['exhibitor_id'] ?? null;
            $subEventId = $jwt['subEventId'] ?? null;

            if (!$exhibitorContactId || !$subEventId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'success' => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
            }

            $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
                ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
                ->join('company_sub_events as cse', 'cse.id = e.sub_event_id', 'left')
                ->select('e.id as exhibitor_id, cse.sub_event_name, e.event_id')
                ->where('e.id', $exhibitorContactId)
                ->where('e.sub_event_id', $subEventId)
                ->get()->getRowArray();

            if (!$exhibitor) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => false,
                    'success' => false,
                    'code'    => 404,
                    'message' => 'Exhibitor not found.',
                    'data'    => null,
                ]);
            }

            // Get all categories for this event
            $categories = $this->db->table('product_categories')
                ->select('id, product_category')
                ->where('event_id', $exhibitor['event_id'])
                ->where('is_deleted', 0)
                ->orderBy('id', 'ASC')
                ->get()->getResultArray();

            if (empty($categories)) {
                return $this->response->setJSON([
                    'status'  => true,
                    'success' => true,
                    'code'    => 200,
                    'message' => 'OK',
                    'data'    => [],
                ]);
            }

            $categoryIds = array_column($categories, 'id');

            // Get all products for these categories
            $products = $this->db->table('products')
                ->select('id, product_category_id, product_name')
                ->whereIn('product_category_id', $categoryIds)
                ->where('is_deleted', 0)
                ->orderBy('id', 'ASC')
                ->get()->getResultArray();

            // Get selected products for this exhibitor
            $selectedProductIds = $this->db->table('exhibitor_deal_in_products')
                ->select('product_id')
                ->where('exhibitor_id', $jwt['exhibitor_id'])
                ->where('is_deleted', 0)
                ->get()->getResultArray();
            // echo $this->db->getlastquery(); die;
            $selectedProductIds = array_map('intval', array_column($selectedProductIds, 'product_id'));

            // Group products by category
            $productsByCategory = [];
            foreach ($products as $product) {
                $productsByCategory[$product['product_category_id']][] = [
                    'id'       => (int) $product['id'],
                    'name'     => $product['product_name'],
                    'selected' => in_array((int) $product['id'], $selectedProductIds, true),
                ];
            }

            // Build final structure
            $result = [];
            foreach ($categories as $category) {
                $result[] = [
                    'id'       => (int) $category['id'],
                    'name'     => $category['product_category'],  // ← This becomes parent.name
                    'children' => $productsByCategory[$category['id']] ?? [],  // ← This becomes children array
                ];
            }

            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'code'    => 200,
                'message' => 'OK',
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'productCategories index failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'success' => false,
                'code'    => 500,
                'message' => 'Something went wrong while fetching product categories.',
                'data'    => null,
            ]);
        }
    }

    public function onlineFormsMenu()
    {
        $jwt = $this->getJwtContext();
        $subEventId = $jwt['subEventId'] ?? null;
        if (!$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }
        $setupQuery = $this->db->table('manual_setups')
            ->select('online_forms_enable_disable, online_forms_open_close')
            ->where('sub_event_id', $subEventId)
            ->where('event_id', $jwt['eventId'])
            ->where('is_deleted', 0)
            ->orderBy('id', 'DESC');
        $setup = $setupQuery->get(1)->getRowArray();
        $defaultEnableDisable = [
            'fascia' => 1,
            'exhibitor_badges' => 1,
            'invitation_tickets' => 1,
            'additional_furniture' => 1,
        ];
        $defaultOpenClose = [
            'fascia' => 1,
            'exhibitor_badges' => 1,
            'invitation_tickets' => 1,
            'additional_furniture' => 1,
        ];
        $enableDisable = $defaultEnableDisable;
        $openClose = $defaultOpenClose;
        if (!empty($setup['online_forms_enable_disable'])) {
            $decoded = json_decode($setup['online_forms_enable_disable'], true);
            if (is_array($decoded)) {
                $enableDisable = array_merge($defaultEnableDisable, $decoded);
            }
        }
        if (!empty($setup['online_forms_open_close'])) {
            $decoded = json_decode($setup['online_forms_open_close'], true);
            if (is_array($decoded)) {
                $openClose = array_merge($defaultOpenClose, $decoded);
            }
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Online forms config fetched successfully.',
            'data' => [
                'online_forms_enable_disable' => $enableDisable,
                'online_forms_open_close'     => $openClose,
            ],
        ]);
    }

    public function updateOnlineFormsSettings()
    {
        try {
            $jwt = $this->getJwtContext();
            $subEventId = $jwt['subEventId'] ?? null;

            if (!$subEventId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'success' => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
            }
            $post = $this->request->getJSON(true);
            $enableDisable = $post['enable_disable'] ?? [];
            $openClose = $post['open_close'] ?? [];
            $formKeys = ['fascia', 'exhibitor_badges', 'invitation_tickets', 'additional_furniture'];
            foreach ($formKeys as $key) {
                if (isset($enableDisable[$key])) $enableDisable[$key] = (int) $enableDisable[$key];
                if (isset($openClose[$key])) $openClose[$key] = (int) $openClose[$key];
            }
            $exists = $this->db->table('manual_setups')
                ->where('sub_event_id', $subEventId)
                ->where('is_deleted', 0)
                ->get()->getRow();
            $data = [
                'online_forms_enable_disable' => json_encode($enableDisable),
                'online_forms_open_close' => json_encode($openClose),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $jwt['userId'] ?? null,
            ];
            if ($exists) {
                $this->db->table('manual_setups')
                    ->where('sub_event_id', $subEventId)
                    ->update($data);
            } else {
                $data['sub_event_id'] = $subEventId;
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $jwt['userId'] ?? null;
                $this->db->table('manual_setups')->insert($data);
            }
            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'code'    => 200,
                'message' => 'Online forms settings updated successfully.',
                'data'    => null,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'updateOnlineFormsSettings failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'success' => false,
                'code'    => 500,
                'message' => 'Something went wrong while updating online forms settings.',
                'data'    => null,
            ]);
        }
    }

    public function fasciaMenu()
    {
        try {
            $jwt = $this->getJwtContext();
            $subEventId = $jwt['subEventId'] ?? null;
            if (!$subEventId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'success' => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
            }

            $manualSetup = $this->db->table('manual_setups')
                ->select('fascia_options')
                ->where('sub_event_id', $subEventId)
                ->where('is_deleted', 0)
                ->get()->getRowArray();

            if (!$manualSetup) {
                return $this->response->setJSON([
                    'status'  => true,
                    'success' => true,
                    'code'    => 200,
                    'message' => 'OK',
                    'data'    => [
                        'fascia_category' => null
                    ]
                ]);
            }

            $fasciaOptions = json_decode($manualSetup['fascia_options'], true);
            $fasciaCategory = null;
            if (isset($fasciaOptions['shell_space']) && $fasciaOptions['shell_space'] == 1) {
                $fasciaCategory = 1;
            } elseif (isset($fasciaOptions['pre_fabricated_shell_space']) && $fasciaOptions['pre_fabricated_shell_space'] == 1) {
                $fasciaCategory = 2;
            } elseif (isset($fasciaOptions['raw_stall_fabricated_by_exhibitor']) && $fasciaOptions['raw_stall_fabricated_by_exhibitor'] == 1) {
                $fasciaCategory = 3;
            }
            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'code'    => 200,
                'message' => 'OK',
                'data'    => [
                    'fascia_category' => $fasciaCategory,
                    'fascia_options' => $fasciaOptions
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'fasciaMenu failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'success' => false,
                'code'    => 500,
                'message' => 'Something went wrong while fetching fascia configuration.',
                'data'    => null,
            ]);
        }
    }

    public function getSubmissionStatus()
    {
        $jwt = $this->getJwtContext();
        $subEventId = $jwt['subEventId'];
        $exhibitorId = $jwt['payload']->sub ?? null;
        $exhibitorNewId = $jwt['payload']->exhibitor_id ?? null;
        if (!$exhibitorId || !$subEventId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized.',
                'data' => null,
            ]);
        }

        $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
            ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
            ->join('company_sub_events as cse', 'cse.id = e.sub_event_id', 'left')
            ->select('e.id as exhibitor_id, e.stall_type_id, e.casual_gst_number, e.brand_logo,
              e.company_product_specialization, e.brand_profile,
              ecp.first_name, ecp.mobile_number, ecp.email,
              cse.sub_event_name')
            ->where('ecp.id', $exhibitorId)
            ->where('e.sub_event_id', $subEventId)
            ->get()->getRowArray();

        $badgeCount = $this->db->table('manual_exhibitor_badges')
            ->where('exhibitor_id', $exhibitorNewId)
            ->where('is_deleted', 0)
            ->countAllResults();

        $orderCount = $this->db->table('orders')
            ->where('exhibitor_id', $exhibitorNewId)
            ->where('sub_event_id', $subEventId)
            ->whereIn('payment_status', ['paid', 'completed', 'success'])
            ->countAllResults();

        $fasciaRow = $this->db->table('stall_categories')
            ->where('exhibitor_id', $exhibitorNewId)
            ->where('sub_event_id', $subEventId)
            ->get()->getRowArray();

        $visitorTicketCount = $this->db->table('visitor_ticket_requests')
            ->where('exhibitor_id', $exhibitorNewId)
            ->where('sub_event_id', $subEventId)
            ->countAllResults();
        $stallTypeId = $exhibitor['stall_type_id'] ?? null;
        $fasciaSubmitted = false;
        if ($stallTypeId == 2) {
            $fasciaSubmitted = !empty($fasciaRow) && !empty($fasciaRow['stall_layout']) && $fasciaRow['status'] !== 'pending';
        } else {
            $fasciaSubmitted = !empty($fasciaRow)
                || !empty($fasciaRow['fabricator_company_name'])
                || !empty($fasciaRow['electricity_requirement'])
                || !empty($fasciaRow['stall_open_side']);
        }

        $casualGstSubmitted = !empty($exhibitor['casual_gst_number']);

        $profileSubmitted = !empty($exhibitor['brand_logo'])
            && !empty($exhibitor['company_product_specialization'])
            && !empty($exhibitor['first_name'])
            && (!empty($exhibitor['mobile_number']) || !empty($exhibitor['email']));

        $badgesSubmitted = $badgeCount > 0;
        $orderSubmitted = $orderCount > 0;
        $visitorTicketSubmitted = $visitorTicketCount > 0;

        $statusList = [
            'profile' => $profileSubmitted,
            'fascia' => $fasciaSubmitted, // <-- Updated logic applied here
            'casual_gst' => $casualGstSubmitted,
            'exhibitor_badges' => $badgesSubmitted,
            'additional_furniture' => $orderSubmitted,
            'visitor_ticket_requests' => $visitorTicketSubmitted,
        ];

        $subEventName = $exhibitor['sub_event_name'] ?? '';
        if ($subEventName === 'Fire India' || $subEventName === 'Drone Expo & Conference' || $subEventName === 'Secure Nation') {
            unset($statusList['casual_gst']);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'code' => 200,
            'message' => 'Submission status fetched successfully.',
            'data' => $statusList,
        ]);
    }

    public function exitPermit()
    {
        try {
            $jwt         = $this->getJwtContext();
            $exhibitorId = $jwt['exhibitor_id'] ?? null;
            $subEventId  = $jwt['subEventId'] ?? null;
            $eventId     = $jwt['eventId'] ?? null;
            if (!$exhibitorId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
            }
            $template = $this->db->table('permits as p')
                ->select('p.permit_name, p.permit_content,
              c.company_logo, c.company_name,
              cse.sub_event_name, cse.venue, cse.venue_city,
              cse.start_date, cse.end_date,
              e.event_name')
                ->join('company_sub_events as cse', 'cse.id = p.sub_event_id', 'left')
                ->join('companies as c', 'c.id = cse.company_id', 'left')
                ->join('company_events as e', 'e.id = p.event_id', 'left')
                ->where('p.event_id', $eventId)
                ->where('p.sub_event_id', $subEventId)
                ->where('LOWER(p.permit_name)', 'exit permit')
                ->where('p.is_deleted', 0)
                ->get()
                ->getRowArray();
            if (!$template || empty($template['permit_content'])) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'PDF template not found.',
                    'data'    => null,
                ]);
            }
            $fullDate = '';

            if (!empty($template['start_date']) && !empty($template['end_date'])) {
                $start = new \DateTime($template['start_date']);
                $end   = new \DateTime($template['end_date']);

                if ($start->format('F Y') === $end->format('F Y')) {
                    // Same month & year
                    $fullDate = strtoupper($start->format('jS')) . ' - ' .
                        strtoupper($end->format('jS')) . ' ' .
                        $end->format('F Y');
                } else {
                    // Different month/year
                    $fullDate = strtoupper($start->format('jS')) . ' ' .
                        $start->format('F') . ' - ' .
                        strtoupper($end->format('jS')) . ' ' .
                        $end->format('F Y');
                }
            }
            $placeholders = [
                '{{venue_city}}'   => $template['venue_city'],
                '{{event_name}}'   => $template['event_name'],
                '{{sub_event_name}}' => $template['sub_event_name'],
                '{{company_name}}' => $template['company_name'],
                '{{date}}'         => date('jS M Y'),
                '{{venue}}'         => $template['venue'],
                '{{venue_city}}'         => $template['venue_city'],
                '{{end_date}}'     => !empty($template['end_date'])
                    ? (new \DateTime($template['end_date']))->format('jS M Y')
                    : '',
                '{{full_date}}'    => $fullDate,
            ];
            $html = str_replace(array_keys($placeholders), array_values($placeholders), $template['permit_content']);
            $html = $this->cleanTrailingContent($html);
            $html = $this->compactPdfHtml($html);
            $fileName = 'exit-permit-' . $exhibitorId . '-' . time() . '.pdf';
            return PdfHelper::download($this->response, $html, $fileName);
        } catch (\Throwable $e) {
            log_message('error', 'exitPermit PDF failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'code'    => 500,
                'message' => 'Something went wrong while generating the PDF.',
                'error'   => $e->getMessage(),
                'data'    => null,
            ]);
        }
    }

    public function welcomeLetter()
    {
        try {
            $jwt         = $this->getJwtContext();
            $exhibitorId = $jwt['exhibitor_id'] ?? null;
            $eventId     = $jwt['eventId'] ?? null;
            $subEventId  = $jwt['subEventId'] ?? null;

            if (!$exhibitorId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
            }

            $template = $this->db->table('letters as l')
                ->select('l.letter_name, l.content, cse.sub_event_name,c.company_name, cse.venue, cse.full_date, cse.start_date, cse.end_date, l.stamp, c.company_logo, e.organisation_name,e.stall_number')
                ->join('company_sub_events as cse', 'cse.id = l.sub_event_id', 'left')
                ->join('companies as c', 'c.id = cse.company_id', 'left')
                ->join('exhibitors as e', 'e.sub_event_id = cse.id', 'left')
                ->where('e.event_id', $eventId)
                ->where(strtolower('l.letter_name'), 'Welcome-Letter')
                ->where('e.id', $exhibitorId)
                ->where('e.sub_event_id', $subEventId)
                ->where('l.is_deleted', 0)
                ->get()
                ->getRowArray();
            /* print_r($template); die; */
            if (!$template || empty($template['content'])) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'PDF template not found.',
                    'data'    => null,
                ]);
            }
            $uploadBaseUrl = rtrim(env('UPLOAD_BASE_URL', getenv('UPLOAD_BASE_URL')), '/');
            $placeholders = [
                '{{company_logo}}'      => !empty($template['company_logo'])
                    ? $uploadBaseUrl . '/' . ltrim($template['company_logo'], '/')
                    : '',
                '{{date}}'              => date('jS M Y'),
                '{{exhibitor_company}}' => $template['organisation_name'],
                '{{company_name}}' => $template['company_name'],
                '{{sub_event_name}}'    => $template['sub_event_name'],
                '{{venue}}'             => $template['venue'],
                '{{full_date}}'         => $template['full_date'],
                '{{stall_number}}'      => $template['stall_number'],
                '{{stamp}}'             => !empty($template['stamp'])
                    ? $uploadBaseUrl . '/' . ltrim($template['stamp'], '/')
                    : '',
            ];

            $html = str_replace(array_keys($placeholders), array_values($placeholders), $template['content']);

            $fileName = 'welcome-letter-' . time() . '.pdf';

            // --- FIX: Pass custom margin options to force it into 1 page ---
            $pdfOptions = [
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 10,
                'margin_bottom' => 10,
            ];

            // Pass the $pdfOptions array as the 4th parameter
            return PdfHelper::download($this->response, $html, $fileName, $pdfOptions);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Welcome Letter Error : ' .
                    $e->getMessage() .
                    ' File : ' .
                    $e->getFile() .
                    ' Line : ' .
                    $e->getLine()
            );

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'code'    => 500,
                'message' => 'Something went wrong while generating the PDF.',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function cleanTrailingContent(string $html): string
    {
        $html = trim($html);
        $html = preg_replace('/(<br\s*\/?>\s*)+$/i', '', $html);
        do {
            $before = $html;
            $html = preg_replace('/<p[^>]*>\s*(&nbsp;|\s|<br\s*\/?>)*\s*<\/p>\s*$/i', '', $html);
        } while ($html !== $before);
        do {
            $before = $html;
            $html = preg_replace('/<div[^>]*>\s*(&nbsp;)?\s*<\/div>\s*$/i', '', $html);
        } while ($html !== $before);
        $html = preg_replace('/page-break-(before|after)\s*:\s*always;?/i', '', $html);
        $html = preg_replace('/break-(before|after)\s*:\s*(always|page);?/i', '', $html);

        return trim($html);
    }

    public function getPendingOrders()
    {
        try {
            $jwt = $this->getJwtContext();
            $exhibitorId = $jwt['payload']->exhibitor_id ?? null;
            if (!$exhibitorId) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status' => false,
                        'message' => 'Unauthorized access'
                    ]);
            }

            $pendingStatusCodes = [0, 1, 3];
            try {
                $pendingOrder = $this->db->table('quotes')
                    ->select('
                id,
                qid,
                ref_no,
                amount,
                q_amount,
                status,
                remarks,
                added_date,
                exhibitor_id,
                currency
            ')
                    ->where('exhibitor_id', $exhibitorId)
                    ->whereIn('status', $pendingStatusCodes)
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
            } catch (\Exception $e) {
                log_message('error', '[getPendingOrders] ' . $e->getMessage());
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status' => false,
                        'message' => 'Failed to load pending orders.'
                    ]);
            }

            if (empty($pendingOrder)) {
                // No order yet — fall back to live exhibitor type
                $isInternational = $this->resolveIsInternational($exhibitorId);
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON([
                        'status' => true,
                        'message' => 'No pending orders found',
                        'data' => [
                            'order' => null,
                            'currency_symbol' => $isInternational ? '$' : '₹'
                        ]
                    ]);
            }

            // currency column now stores the symbol directly ('₹' or '$')
            $currencySymbol = $pendingOrder['currency'] ?: '₹';

            $statusLabels = [
                0 => 'draft',
                1 => 'sent',
                2 => 'accepted',
                3 => 'rejected',
            ];
            $items = $this->db->table('quotes_details qd')
                ->select('
                qd.id,
                qd.qid,
                qd.item_id,
                qd.item_name,
                qd.quantity,
                qd.line_total
            ')
                ->join('items i', 'i.id = qd.item_id', 'left')
                ->where('qd.qid', $pendingOrder['qid'])
                ->get()
                ->getResultArray();
            $pendingOrder['items'] = $items;
            $pendingOrder['items_count'] = count($items);
            $pendingOrder['status_label'] = $statusLabels[(int) $pendingOrder['status']] ?? 'unknown';

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'  => true,
                    'message' => 'Pending order retrieved successfully',
                    'data'    => [
                        'order'           => $pendingOrder,
                        'currency_symbol' => $currencySymbol,
                    ]
                ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getPendingOrders: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'An error occurred while fetching pending orders: ' . $e->getMessage()
                ]);
        }
    }

    public function furnitureOptOut()
    {
        try {
            $jwt = $this->getJwtContext();
            $vendorId   = $jwt['vendorId'] ?? null;
            $subEventId = $jwt['subEventId'] ?? null;
            if (!$vendorId || !$subEventId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
            }
            $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
                ->join('exhibitors as e', 'ecp.exhibitor_id = e.id', 'left')
                ->select('e.id as exhibitor_id, e.sub_event_id, e.is_need_additional_furniture')
                ->where('ecp.id', $vendorId)
                ->where('e.sub_event_id', $subEventId)
                ->get()->getRowArray();

            if (!$exhibitor) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Exhibitor not found for this sub-event.',
                    'data'    => null,
                ]);
            }
            $post = $this->request->getJSON(true) ?? $this->request->getPost();
            $isNeedFurniture = $post['is_need_additional_furniture'] ?? null;
            if ($isNeedFurniture === null || !in_array((string) $isNeedFurniture, ['0', '1'], true)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => false,
                    'code'    => 422,
                    'message' => 'is_need_additional_furniture must be 0 or 1.',
                    'data'    => null,
                ]);
            }
            $isNeedFurniture = (int) $isNeedFurniture;
            if ((int) $exhibitor['is_need_additional_furniture'] === $isNeedFurniture) {
                return $this->response->setStatusCode(200)->setJSON([
                    'status'  => true,
                    'code'    => 200,
                    'message' => 'Preference already saved.',
                    'data'    => [
                        'is_need_additional_furniture' => $isNeedFurniture,
                    ],
                ]);
            }

            $updated = $this->db->table('exhibitors')
                ->where('id', $exhibitor['exhibitor_id'])
                ->where('sub_event_id', $subEventId)
                ->update([
                    'is_need_additional_furniture' => $isNeedFurniture,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($updated === false) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => false,
                    'code'    => 500,
                    'message' => 'Unable to save your preference.',
                    'data'    => null,
                ]);
            }

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => $isNeedFurniture === 0
                    ? 'Preference saved successfully.'
                    : 'Additional furniture re-enabled successfully.',
                'data'    => [
                    'is_need_additional_furniture' => $isNeedFurniture,
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'furnitureOptOut failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'code'    => 500,
                'message' => 'Something went wrong while saving your preference.',
                'data'    => null,
            ]);
        }
    }

    public function pending_payments()
    {
        $payload    = JwtPayload::get();
        $vendorId   = $payload->exhibitor_id ?? null;
        $subEventId = $payload->sub_event_id ?? null;

        if (!$vendorId || !$subEventId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null
                ]);
        }

        $isInternational = $this->resolveIsInternational($vendorId);
        $currencySymbol  = $isInternational ? '$' : '₹';

        $fascia = $this->db->table('stall_categories')
            ->where('exhibitor_id', $vendorId)
            ->where('sub_event_id', $subEventId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();

        $stallTypeId = (int) ($fascia->stall_type_id ?? 0);

        $isRawSpace   = ($stallTypeId === 2);
        $isShellSpace = ($stallTypeId === 3);

        if (!$isRawSpace && !$isShellSpace) {

            $hasElectricityInQuotes = $this->db->table('quotes_details qd')
                ->select('qd.id')
                ->join('quotes q', 'q.qid = qd.qid')
                ->join('items i', 'i.id = qd.item_id', 'left')
                ->where('q.exhibitor_id', $vendorId)
                ->where('q.event_id', $subEventId)
                ->where('q.status', 0)
                ->where('i.is_electricity', 1)
                ->get()
                ->getRow();

            $hasElectricityInCart = $this->db->table('cart c')
                ->select('c.id')
                ->join('items i', 'i.id = c.item_id', 'left')
                ->where('c.vendor_id', $vendorId)
                ->where('c.sub_event_id', $subEventId)
                ->where('c.is_deleted', 0)
                ->where('i.is_electricity', 1)
                ->get()
                ->getRow();

            $hasFurnitureInQuotes = $this->db->table('quotes_details qd')
                ->select('qd.id')
                ->join('quotes q', 'q.qid = qd.qid')
                ->join('items i', 'i.id = qd.item_id', 'left')
                ->where('q.exhibitor_id', $vendorId)
                ->where('q.event_id', $subEventId)
                ->where('q.status', 0)
                ->groupStart()
                ->where('i.is_electricity', null)
                ->orWhere('i.is_electricity', 0)
                ->groupEnd()
                ->get()
                ->getRow();

            $hasFurnitureInCart = $this->db->table('cart c')
                ->select('c.id')
                ->join('items i', 'i.id = c.item_id', 'left')
                ->where('c.vendor_id', $vendorId)
                ->where('c.sub_event_id', $subEventId)
                ->where('c.is_deleted', 0)
                ->groupStart()
                ->where('i.is_electricity', null)
                ->orWhere('i.is_electricity', 0)
                ->groupEnd()
                ->get()
                ->getRow();

            if ($hasElectricityInQuotes || $hasElectricityInCart) {
                $isRawSpace   = true;
                $isShellSpace = false;
            } elseif ($hasFurnitureInQuotes || $hasFurnitureInCart) {
                $isRawSpace   = false;
                $isShellSpace = true;
            }
        }

        $payments = [
            'furniture'   => null,
            'electricity' => null,
        ];

        $pendingQuotes = $this->db->table('quotes q')
            ->select('q.id, q.qid, q.currency, q.q_amount, q.amount, q.status, q.ref_no, q.remarks')
            ->where('q.exhibitor_id', $vendorId)
            ->where('q.event_id', $subEventId)
            ->where('q.status', 0)
            ->orderBy('q.id', 'DESC')
            ->get()
            ->getResultArray();

        $furnitureQuote   = null;
        $electricityQuote = null;

        foreach ($pendingQuotes as $quote) {

            $details = $this->db->table('quotes_details qd')
                ->select('
                qd.id,
                qd.qid,
                qd.item_id,
                qd.item_name,
                qd.quantity,
                qd.unit_price,
                qd.sale_price,
                qd.line_total,
                qd.is_early_bird,
                qd.added_date,
                qd.updated_date,
                i.item_name AS master_item_name,
                i.item_image,
                i.is_electricity
            ')
                ->join('items i', 'i.id = qd.item_id', 'left')
                ->where('qd.qid', $quote['qid'])
                ->orderBy('qd.id', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($details)) {
                continue;
            }

            $furnitureItems   = [];
            $electricityItems = [];

            foreach ($details as $item) {

                $isElectricity = $item['is_electricity'] !== null
                    && (int) $item['is_electricity'] === 1;

                $item['item_name'] = !empty($item['item_name'])
                    ? $item['item_name']
                    : $item['master_item_name'];

                if ($isElectricity) {
                    $electricityItems[] = $item;
                } else {
                    $furnitureItems[] = $item;
                }
            }

            if (!empty($electricityItems) && $electricityQuote === null) {
                $electricityQuote = [
                    'qid'   => $quote['qid'],
                    'items' => $electricityItems
                ];
            }

            if (!empty($furnitureItems) && $furnitureQuote === null) {
                $furnitureQuote = [
                    'qid'   => $quote['qid'],
                    'items' => $furnitureItems
                ];
            }

            if ($furnitureQuote !== null && (!$isRawSpace || $electricityQuote !== null)) {
                break;
            }
        }

        if ($isShellSpace) {
            $electricityQuote = null;
        }

        if ($furnitureQuote !== null) {

            $furnitureItems = $furnitureQuote['items'];

            if ($isShellSpace && !empty($furnitureItems)) {
                $furnitureItems = [end($furnitureItems)];
            }

            $furnitureSubtotal = 0;

            foreach ($furnitureItems as &$item) {

                $quantity = (int) ($item['quantity'] ?? 1);
                if ($quantity <= 0) {
                    $quantity = 1;
                }

                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineTotal = round($unitPrice * $quantity, 2);

                $item['item_id']        = (int) $item['item_id'];
                $item['item_name']      = $item['item_name'];
                $item['quantity']       = $quantity;
                $item['unit_price']     = $unitPrice;
                $item['sale_price']     = (float) ($item['sale_price'] ?? $unitPrice);
                $item['line_total']     = $lineTotal;
                $item['is_electricity'] = 0;

                $furnitureSubtotal += $lineTotal;
            }
            unset($item);

            $payments['furniture'] = [
                'source'          => 'quotation',
                'qid'             => $furnitureQuote['qid'],
                'items'           => array_values($furnitureItems),
                'subtotal'        => number_format($furnitureSubtotal, 2, '.', ''),
                'tax'             => number_format($furnitureSubtotal * 0.18, 2, '.', ''),
                'total'           => number_format($furnitureSubtotal * 1.18, 2, '.', ''),
                'currency_symbol' => $currencySymbol,
                'status'          => 'pending'
            ];
        }

        if ($isRawSpace && $electricityQuote !== null) {

            $electricityItems = $electricityQuote['items'];

            $electricitySubtotal = 0;

            foreach ($electricityItems as &$item) {

                $quantity = (int) ($item['quantity'] ?? 1);
                if ($quantity <= 0) {
                    $quantity = 1;
                }

                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineTotal = round($unitPrice * $quantity, 2);

                $item['item_id']        = (int) $item['item_id'];
                $item['item_name']      = $item['item_name'];
                $item['quantity']       = $quantity;
                $item['unit_price']     = $unitPrice;
                $item['sale_price']     = (float) ($item['sale_price'] ?? $unitPrice);
                $item['line_total']     = $lineTotal;
                $item['is_electricity'] = 1;

                $electricitySubtotal += $lineTotal;
            }
            unset($item);

            $payments['electricity'] = [
                'source'          => 'quotation',
                'qid'             => $electricityQuote['qid'],
                'items'           => array_values($electricityItems),
                'subtotal'        => number_format($electricitySubtotal, 2, '.', ''),
                'tax'             => number_format($electricitySubtotal * 0.18, 2, '.', ''),
                'total'           => number_format($electricitySubtotal * 1.18, 2, '.', ''),
                'currency_symbol' => $currencySymbol,
                'status'          => 'pending'
            ];
        }

        $needFurnitureFromCart   = ($payments['furniture'] === null);
        $needElectricityFromCart = ($isRawSpace && $payments['electricity'] === null);

        if ($needFurnitureFromCart || $needElectricityFromCart) {

            $cartItems = $this->db->table('cart c')
                ->select('
                c.id,
                c.item_id,
                c.quantity,
                c.price,
                c.original_price,
                c.is_early_bird,
                i.item_name,
                i.item_image,
                i.is_electricity
            ')
                ->join('items i', 'i.id = c.item_id', 'left')
                ->where('c.vendor_id', $vendorId)
                ->where('c.sub_event_id', $subEventId)
                ->where('c.is_deleted', 0)
                ->where('i.is_deleted', 0)
                ->orderBy('c.id', 'ASC')
                ->get()
                ->getResultArray();

            $furnitureItems   = [];
            $electricityItems = [];

            foreach ($cartItems as $item) {
                if ($item['is_electricity'] !== null && (int) $item['is_electricity'] === 1) {
                    $electricityItems[] = $item;
                } else {
                    $furnitureItems[] = $item;
                }
            }

            if ($isShellSpace) {
                $electricityItems = [];
                if (!empty($furnitureItems)) {
                    $furnitureItems = [end($furnitureItems)];
                }
            }

            if ($needFurnitureFromCart && !empty($furnitureItems)) {

                $subtotal = 0;

                foreach ($furnitureItems as &$item) {
                    $quantity = (int) ($item['quantity'] ?? 1);
                    $price    = (float) ($item['price'] ?? 0);

                    $item['item_id']        = (int) $item['item_id'];
                    $item['quantity']       = $quantity;
                    $item['unit_price']     = $price;
                    $item['sale_price']     = (float) ($item['original_price'] ?? $price);
                    $item['is_electricity'] = 0;
                    $item['line_total']     = round($price * $quantity, 2);

                    $subtotal += $item['line_total'];
                }
                unset($item);

                $payments['furniture'] = [
                    'source'          => 'cart',
                    'items'           => array_values($furnitureItems),
                    'subtotal'        => number_format($subtotal, 2, '.', ''),
                    'tax'             => number_format($subtotal * 0.18, 2, '.', ''),
                    'total'           => number_format($subtotal * 1.18, 2, '.', ''),
                    'currency_symbol' => $currencySymbol,
                    'status'          => 'pending'
                ];
            }

            if ($needElectricityFromCart && !empty($electricityItems)) {

                $subtotal = 0;

                foreach ($electricityItems as &$item) {
                    $quantity = (int) ($item['quantity'] ?? 1);
                    $price    = (float) ($item['price'] ?? 0);

                    $item['item_id']        = (int) $item['item_id'];
                    $item['quantity']       = $quantity;
                    $item['unit_price']     = $price;
                    $item['is_electricity'] = 1;
                    $item['line_total']     = round($price * $quantity, 2);

                    $subtotal += $item['line_total'];
                }
                unset($item);

                $payments['electricity'] = [
                    'source'          => 'cart',
                    'items'           => array_values($electricityItems),
                    'subtotal'        => number_format($subtotal, 2, '.', ''),
                    'tax'             => number_format($subtotal * 0.18, 2, '.', ''),
                    'total'           => number_format($subtotal * 1.18, 2, '.', ''),
                    'currency_symbol' => $currencySymbol,
                    'status'          => 'pending'
                ];
            }
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Pending payments fetched.',
                'data'    => [
                    'stall_type'      => $isRawSpace ? 'raw' : ($isShellSpace ? 'shell' : 'unknown'),
                    'is_raw_space'    => $isRawSpace,
                    'currency_symbol' => $currencySymbol,
                    'payments'        => $payments
                ]
            ]);
    }
    public function editRequest()
    {
        $jwt = $this->getJwtContext();

        $exhibitorContactId = $jwt['vendorId'] ?? null;
        $exhibitorId        = $jwt['exhibitor_id'] ?? null;
        $subEventId         = $jwt['subEventId'] ?? null;

        if (!$exhibitorContactId || !$exhibitorId) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unauthorized.',
            ])->setStatusCode(401);
        }

        $data   = $this->request->getJSON(true);
        $detail = trim($data['detail'] ?? '');

        if ($detail === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Detail is required.',
            ])->setStatusCode(422);
        }

        $exhibitor = $this->db->table('exhibitor_contact_persons as ecp')
            ->join('exhibitors as e', 'e.id = ecp.exhibitor_id', 'left')
            ->select('e.organisation_name, e.brand_name, ecp.first_name, ecp.last_name, ecp.email, ecp.country_code, ecp.mobile_number')
            ->where('ecp.id', $exhibitorContactId)
            ->get()
            ->getRowArray();

        if (!$exhibitor) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Exhibitor not found.',
            ])->setStatusCode(404);
        }

        $contactPerson = trim(($exhibitor['first_name'] ?? '') . ' ' . ($exhibitor['last_name'] ?? ''));
        $contactNumber = trim(($exhibitor['country_code'] ?? '') . ' ' . ($exhibitor['mobile_number'] ?? ''));
        $contactEmail  = $exhibitor['email'] ?? '';

        $setupRow = $this->db->table('manual_setups')
            ->select('notification_email')
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRowArray();

        $recipients = [];

        if (!empty($setupRow['notification_email'])) {
            $recipients = array_filter(
                array_map('trim', explode(',', $setupRow['notification_email']))
            );

            $recipients = array_values(
                array_filter(
                    $recipients,
                    fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL)
                )
            );
        }

        if (empty($recipients)) {
            log_message(
                'warning',
                'No valid notification_email found in manual_setup for sub_event_id: ' . $subEventId
            );

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Request could not be sent: no notification recipients configured.',
            ])->setStatusCode(500);
        }

        $subject = 'Profile Edit Request — ' .
            ($exhibitor['organisation_name'] ?? $exhibitor['brand_name'] ?? 'Exhibitor');

        $htmlBody = "
<div style='font-family: \"Segoe UI\", Arial, sans-serif; background-color: #f4f6f8; padding: 30px 0; margin: 0;'>
  <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);'>

    <!-- Header -->
    <tr>
      <td style='background-color: #1a2b49; padding: 24px 32px;'>
        <h1 style='color: #ffffff; font-size: 20px; margin: 0; font-weight: 600;'>Exhibitor Change Request</h1>
      </td>
    </tr>

    <!-- Body -->
    <tr>
      <td style='padding: 32px;'>

        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 24px;'>
          <tr>
            <td style='padding: 8px 0; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; width: 160px;'>Organisation</td>
            <td style='padding: 8px 0; color: #111827; font-size: 15px; font-weight: 500;'>" . htmlspecialchars($exhibitor['organisation_name'] ?? '') . "</td>
          </tr>
          <tr>
            <td style='padding: 8px 0; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;'>Brand</td>
            <td style='padding: 8px 0; color: #111827; font-size: 15px; font-weight: 500;'>" . htmlspecialchars($exhibitor['brand_name'] ?? '') . "</td>
          </tr>
          <tr>
            <td style='padding: 8px 0; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: top;'>Contact Person</td>
            <td style='padding: 8px 0; color: #111827; font-size: 15px;'>
              <span style='font-weight: 500;'>" . htmlspecialchars($contactPerson) . "</span><br>
              <span style='color: #4b5563; font-size: 14px;'>" . htmlspecialchars($contactEmail) . " &bull; " . htmlspecialchars($contactNumber) . "</span>
            </td>
          </tr>
        </table>

        <div style='border-top: 1px solid #e5e7eb; padding-top: 20px;'>
          <p style='margin: 0 0 10px 0; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;'>Requested Change</p>
          <div style='background-color: #f9fafb; border-left: 3px solid #1a2b49; padding: 16px 18px; border-radius: 4px; color: #111827; font-size: 15px; line-height: 1.6;'>
            " . nl2br(htmlspecialchars($detail)) . "
          </div>
        </div>

      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td style='background-color: #f9fafb; padding: 18px 32px; border-top: 1px solid #e5e7eb;'>
        <p style='margin: 0; color: #9ca3af; font-size: 12px;'>This is an automated notification regarding an exhibitor profile change request.</p>
      </td>
    </tr>

  </table>
</div>
";

        $failedRecipients = [];
        $errors = [];

        foreach ($recipients as $recipientEmail) {
            try {
                $ok = sendEmail(
                    toEmail: $recipientEmail,
                    toName: $recipientEmail,
                    subject: $subject,
                    htmlBody: $htmlBody
                );

                if (!$ok) {
                    $failedRecipients[] = $recipientEmail;
                    $errors[] = [
                        'recipient' => $recipientEmail,
                        'message'   => 'Email sending failed.',
                    ];
                }
            } catch (\Throwable $e) {
                $failedRecipients[] = $recipientEmail;

                $errors[] = [
                    'recipient' => $recipientEmail,
                    'message'   => $e->getMessage(),
                    'error'     => $e->getFile() . ':' . $e->getLine(),
                ];

                log_message(
                    'error',
                    'Edit request email exception for ' . $recipientEmail . ': ' . $e->getMessage()
                );
            }
        }

        if (count($failedRecipients) === count($recipients)) {
            log_message(
                'error',
                'Edit request email failed for all recipients: ' .
                    implode(', ', $failedRecipients)
            );

            return $this->response->setJSON([
                'status'            => false,
                'message'           => 'Email failed for all recipients.',
                'failed_recipients' => $failedRecipients,
                'errors'            => $errors,
            ])->setStatusCode(500);
        }

        if (!empty($failedRecipients)) {
            log_message(
                'warning',
                'Edit request email failed for some recipients: ' .
                    implode(', ', $failedRecipients)
            );

            return $this->response->setJSON([
                'status'            => true,
                'message'           => 'Profile edit request sent successfully to some recipients.',
                'failed_recipients' => $failedRecipients,
                'errors'            => $errors,
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Profile edit request sent successfully.',
        ]);
    }
    private function compactPdfHtml(string $html): string
    {
        $compactCss = <<<CSS
<style>
    body, p, td, th { line-height: 1.3 !important; }
    p { margin: 4px 0 !important; }
    table { margin-bottom: 6px !important; }
    td, th { padding: 3px 6px !important; }
    h1, h2, h3, h4 { margin: 4px 0 !important; }
    br + br { display: none; }
</style>
CSS;

        if (stripos($html, '</head>') !== false) {
            return preg_replace('/<\/head>/i', $compactCss . '</head>', $html, 1);
        }

        return $compactCss . $html;
    }

    public function participationLetters()
    {
        try {
            $jwt         = $this->getJwtContext();
            $exhibitorId = $jwt['exhibitor_id'] ?? null;
            $eventId     = $jwt['eventId'] ?? null;
            $subEventId  = $jwt['subEventId'] ?? null;

            if (!$exhibitorId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                    'data'    => null,
                ]);
            }

            $template = $this->db->table('letters as l')
                ->select('l.letter_name, l.content, cse.sub_event_name, cse.venue, cse.full_date, cse.start_date, cse.end_date, l.stamp, c.company_logo, e.organisation_name,e.stall_number')
                ->join('company_sub_events as cse', 'cse.id = l.sub_event_id', 'left')
                ->join('companies as c', 'c.id = cse.company_id', 'left')
                ->join('exhibitors as e', 'e.sub_event_id = cse.id', 'left')
                ->where('e.event_id', $eventId)
                ->where(strtolower('l.letter_name'), 'participation letter')
                ->where('e.id', $exhibitorId)
                ->where('e.sub_event_id', $subEventId)
                ->where('l.is_deleted', 0)
                ->get()
                ->getRowArray();
            /* print_r($template); die; */
            if (!$template || empty($template['content'])) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'PDF template not found.',
                    'data'    => null,
                ]);
            }
            $uploadBaseUrl = rtrim(env('UPLOAD_BASE_URL', getenv('UPLOAD_BASE_URL')), '/');
            $placeholders = [
                '{{company_logo}}'      => !empty($template['company_logo'])
                    ? $uploadBaseUrl . '/' . ltrim($template['company_logo'], '/')
                    : '',
                '{{date}}'              => date('jS M Y'),
                '{{exhibitor_company}}' => $template['organisation_name'],
                '{{sub_event_name}}'    => $template['sub_event_name'],
                '{{venue}}'             => $template['venue'],
                '{{full_date}}'         => $template['full_date'],
                '{{stall_number}}'      => $template['stall_number'],
                '{{stamp}}'             => !empty($template['stamp'])
                    ? $uploadBaseUrl . '/' . ltrim($template['stamp'], '/')
                    : '',
            ];

            $html = str_replace(array_keys($placeholders), array_values($placeholders), $template['content']);

            $fileName = 'participation-letter-' . time() . '.pdf';

            // --- FIX: Pass custom margin options to force it into 1 page ---
            $pdfOptions = [
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 10,
                'margin_bottom' => 10,
            ];

            // Pass the $pdfOptions array as the 4th parameter
            return PdfHelper::download($this->response, $html, $fileName, $pdfOptions);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Participation Letter Error : ' .
                    $e->getMessage() .
                    ' File : ' .
                    $e->getFile() .
                    ' Line : ' .
                    $e->getLine()
            );

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'code'    => 500,
                'message' => 'Something went wrong while generating the PDF.',
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
