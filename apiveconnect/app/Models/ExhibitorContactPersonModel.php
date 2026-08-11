<?php

namespace App\Models;

use CodeIgniter\Model;

class ExhibitorContactPersonModel extends Model
{
    protected $table = 'exhibitor_contact_persons';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'first_name',
        'exhibitor_id',
        'email',
        'mobile_number',
        'otp',
        'otp_expire_at',
        'otp_last_sent_at',
        'otp_resend_count',
        'otp_verified'
    ];

    public function findByIdentifier(
        string $identifier,
        int $subEventId
    ) {
        return $this->findContactPersonByIdentifierAndSubEvent($identifier, $subEventId);
    }

    public function findContactPersonByIdentifierAndSubEvent(
        string $identifier,
        int $subEventId
    ) {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }
        $cleanMobile = preg_replace('/[^0-9]/', '', $identifier);
        $builder = $this->db
            ->table('exhibitor_contact_persons ecp')
            ->select('
                    ecp.id,
                    ecp.first_name,
                    ecp.email,
                    ecp.mobile_number,
                    ecp.exhibitor_id,
                    ecp.otp,
                    ecp.otp_expire_at,
                    ecp.otp_last_sent_at,
                    e.exhibitor_type,
                    ecp.otp_resend_count,
                    ecp.otp_verified
                    ')
            ->join(
                'exhibitors e',
                'e.id = ecp.exhibitor_id',
                'inner'
            );
        $builder->groupStart()
            ->where('ecp.email', $identifier)
            ->orWhere('ecp.mobile_number', $identifier);
        if ($cleanMobile !== '') {
            $builder->orWhere(
                'ecp.mobile_number',
                $cleanMobile
            );
        }
        $builder->groupEnd();
        $builder->where(
            'e.sub_event_id',
            $subEventId
        );
        $builder->where(
            'e.is_deleted',
            0
        );
        $result = $builder
            ->limit(1)
            ->get()
            ->getRow();
        return $result;
    }

    public function findContactPersonById($userId)
    {
        return $this->db->table('exhibitor_contact_persons ecp')
            ->select('
                    ecp.id,
                    ecp.first_name,
                    ecp.email,
                    ecp.mobile_number,
                    ecp.exhibitor_id,
                    ecp.otp,
                    ecp.otp_expire_at,
                    ecp.otp_last_sent_at,
                    ecp.otp_resend_count,
                    ecp.otp_verified
                ')
            ->where('ecp.id', $userId)
            ->limit(1)
            ->get()
            ->getRow();
    }

    public function getContactPersonByEmailAndSubEvent(
        string $email,
        int $subEventId
    ) {
        return $this->db->table('exhibitor_contact_persons ecp')
            ->select('ecp.id, ecp.first_name, ecp.email, ecp.exhibitor_id, ecp.password')
            ->join('exhibitors e', 'e.id = ecp.exhibitor_id', 'inner')
            ->where('ecp.email', $email)
            ->where('e.sub_event_id', $subEventId)
            ->limit(1)
            ->get()
            ->getRow();
    }

    public function getSubEvents(int $subEventId)
    {
        $today = date('Y-m-d');
        return $this->db->table('company_events')
            ->join('company_sub_events cse', 'cse.event_id = company_events.id', 'left')
            ->select('
                    company_events.id,
                    company_events.event_name,
                    cse.id as sub_event_id,
                    cse.sub_event_name,
                    cse.start_date,
                    cse.end_date,
                    cse.sub_event_logo,
                    cse.sub_event_date_image
                ')
            ->where('cse.id', $subEventId)
            ->where('cse.start_date >=', $today)
            ->orderBy('cse.start_date', 'ASC')
            ->get()
            ->getRow();
    }

    public function updateRecord(int $id, array $data)
    {
        $this->set($data)
            ->where($this->primaryKey, $id)
            ->update();
        return $this->db->affectedRows();
    }

    public function getById($table, $where = [], $columns = '*', $idField = 'id')
    {
        if (!is_array($where)) {
            $where = [$idField => $where];
        }
        $query = $this->db->table($table)->select($columns);
        foreach ($where as $field => $value) {
            $query->where($field, $value);
        }
        return $query->get()->getRow();
    }

    public function getProfile($exhibitor_id)
    {
       
        $columns = [
            'e.id as exhibitor_id',
            'e.brand_name',
            'e.brand_profile',
            'e.organisation_name',
            'e.landline',
            'e.stall_number',
            'e.stall_size',
            'e.organisation_address',
            'e.exhibitor_type',
            'e.gst_number',
            'e.casual_gst_number',
            'e.casual_gst_document',
            'e.company_product_specialization',
            'e.name_on_certificate_memento',
            'ms.manual_due_date',
            'ms.fascia_due_date',
            'ms.additional_due_date',
            'ms.exhibitor_badge_due_date',
            'ms.visitor_invitation_due_date',
        ];

        foreach (['brand_logo', 'app_logo'] as $optionalColumn) {
            if ($this->tableHasColumn('exhibitors', $optionalColumn)) {
                $columns[] = "e.$optionalColumn";
            }
        }

        return $this->db->table('exhibitor_contact_persons ecp')
            ->select(implode(',', $columns) . ',ecp.first_name as contact_person,ecp.email as contact_email,ecp.mobile_number as contact_number,ce.id as event_id,ce.event_name,e.name_on_certificate_memento,e.exhibitor_type, ms.manual_due_date, ms.fascia_due_date, ms.additional_due_date, ms.visitor_invitation_due_date, ms.exhibitor_badge_due_date')
            ->join('exhibitors e', 'e.id = ecp.exhibitor_id', 'left')
            ->join('manual_setups ms', 'ms.sub_event_id = e.sub_event_id', 'left')
            ->join('company_events ce', 'e.event_id = ce.id', 'left')
            ->where('ecp.id', $exhibitor_id)
            ->limit(1)
            ->get()
            ->getRow();
    }

    // ✅ Add this private method to the model
    private function tableHasColumn(string $table, string $column): bool
    {
        return $this->db->fieldExists($column, $table);
    }

    public function getEventTheme(int $subEventId): array
    {
        $row = $this->db->table('manual_setups')
            ->select('theme_color, theme_color_secondary, theme_pattern')
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRowArray();

        return [
            'primary'   => $row['theme_color'] ?? '#1a1a2e',
            'secondary' => $row['theme_color_secondary'] ?? '#16213e',
            'pattern'   => $row['theme_pattern'] ?? 'diagonal', // diagonal | radial | solid
        ];
    }

    public function buildBadgeViewData(int $badgeId, int $vendorId, int $subEventId, string $fallbackPhotoPath = ''): ?array
    {
        $badge = $this->getBadgeForVendor($badgeId, $vendorId);

        if (!$badge) {
            return null;
        }

        $eventName = $this->getEventName($subEventId);
        $theme     = $this->getEventTheme($subEventId);

        $photoBase64 = $this->pathToBase64($badge['photo_path'] ?? null)
            ?: $this->pathToBase64($fallbackPhotoPath);

        $uniqueValue = (string) ($badge['unique_id'] ?? $badge['badge_code'] ?? $badge['id']);
        $qrBase64    = $this->generateQrBase64($uniqueValue);

        $fullName = trim(($badge['fname'] ?? '') . ' ' . ($badge['lname'] ?? ''));

        return [
            'event_name'     => $eventName ?: 'EXHIBITOR EVENT',
            'full_name'      => $fullName,
            'company_name'   => $badge['company_name'] ?? '',
            'theme_primary'  => $theme['primary'],
            'theme_secondary' => $theme['secondary'],
            'theme_pattern'  => $theme['pattern'],
            'photo'          => $photoBase64,
            'qr'             => $qrBase64,
        ];
    }
}
