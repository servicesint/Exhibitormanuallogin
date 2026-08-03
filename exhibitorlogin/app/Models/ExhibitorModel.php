<?php

namespace App\Models;

use CodeIgniter\Model;

class ExhibitorModel extends Model
{
    private function tableHasColumn(string $table, string $column): bool
    {
        $fields = $this->db->getFieldData($table);
        foreach ($fields as $field) {
            if ($field->name === $column) {
                return true;
            }
        }
        return false;
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
            'e.company_product_specialization',

        ];

        foreach (['brand_logo', 'company_logo', 'app_logo'] as $optionalColumn) {
            if ($this->tableHasColumn('exhibitors', $optionalColumn)) {
                $columns[] = "e.$optionalColumn";
            }
        }

        return $this->db->table('exhibitor_contact_persons ecp')
            ->select(implode(',', $columns) . ',ecp.first_name as contact_person,ecp.email as contact_email,ecp.mobile_number as contact_number,ce.id as event_id,ce.event_name')
            ->join('exhibitors e', 'e.id = ecp.exhibitor_id', 'left')
            ->join('company_events ce', 'e.event_id = ce.id', 'left')
            ->where('ecp.exhibitor_id', $exhibitor_id)
            ->limit(1)
            ->get()
            ->getRow();
    }

    public function getActiveSubEvents($event_id)
    {
        $today = date('Y-m-d');
        return $this->db->table('company_events')
            ->join('company_sub_events cse', 'cse.event_id = company_events.id', 'left')
            ->select('company_events.id, company_events.event_name, cse.id as sub_event_id, cse.sub_event_name, cse.start_date, cse.end_date, cse.sub_event_logo,cse.sub_event_name')
            ->where('cse.event_id', $event_id)
            ->where('cse.start_date >=', $today)
            ->orderBy('cse.start_date', 'ASC')
            ->get()
            ->getResult();
    }
    public function getSubEvents($event_id)
    {
        $today = date('Y-m-d');
        return $this->db->table('company_events')
            ->join('company_sub_events cse', 'cse.event_id = company_events.id', 'left')
            ->select('company_events.id, company_events.event_name, cse.id as sub_event_id, cse.sub_event_name, cse.start_date, cse.end_date, cse.sub_event_logo,cse.sub_event_name,cse.sub_event_date_image')
            ->where('cse.id', $event_id)
            ->where('cse.start_date >=', $today)
            ->orderBy('cse.start_date', 'ASC')
            ->get()
            ->getRow();
    }

    public function findContactPersonByIdentifierAndSubEvent(string $identifier, int $subEventId)
    {
        $identifier = trim($identifier);
        $cleanMobile = preg_replace('/[^0-9]/', '', $identifier);

        return $this->db->table('exhibitor_contact_persons ecp')
            ->select('ecp.id, ecp.first_name, ecp.email, ecp.mobile_number, ecp.exhibitor_id, ecp.password')
            ->join('exhibitors e', 'e.id = ecp.exhibitor_id', 'inner')
            ->where('e.sub_event_id', $subEventId)
            ->groupStart()
            ->where('ecp.email', strtolower($identifier))
            ->orWhere('ecp.email', $identifier)
            ->orWhere('ecp.mobile_number', $identifier)
            ->orWhere('ecp.mobile_number', $cleanMobile)
            ->groupEnd()
            ->limit(1)
            ->get()
            ->getRow();
    }

    public function findContactPersonById($userId)
    {
        return $this->db->table('exhibitor_contact_persons ecp')
            ->select('ecp.id, ecp.first_name, ecp.email, ecp.mobile_number, ecp.exhibitor_id, ecp.password')
            ->where('ecp.id', $userId)
            ->limit(1)
            ->get()
            ->getRow();
    }

    public function getContactPersonByEmailAndSubEvent(string $email, int $subEventId)
    {
        return $this->db->table('exhibitor_contact_persons ecp')
            ->select('ecp.id, ecp.first_name, ecp.email, ecp.exhibitor_id, ecp.password')
            ->join('exhibitors e', 'e.id = ecp.exhibitor_id', 'inner')
            ->where('ecp.email', $email)
            ->where('e.sub_event_id', $subEventId)
            ->limit(1)
            ->get()
            ->getRow();
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

    public function saveProfile(int $exhibitorId, array $data): bool
    {

        $db = $this->db;
        $db->transStart();
        $exhibitorData = [];
        $mapExhibitor = ['organisation_name', 'brand_profile', 'landline', 'stall_number', 'stall_size', 'organisation_address', 'brand_name', 'stall_number'];
        foreach ($mapExhibitor as $k) {
            if (array_key_exists($k, $data)) {
                $exhibitorData[$k] = $data[$k];
            }
        }
        foreach (['brand_logo', 'company_product_specialization', 'app_logo'] as $optionalColumn) {
            if ($this->tableHasColumn('exhibitors', $optionalColumn) && array_key_exists($optionalColumn, $data)) {
                $exhibitorData[$optionalColumn] = $data[$optionalColumn];
            }
        }
        if (!empty($exhibitorData)) {
            $db->table('exhibitors')->where('id', $exhibitorId)->update($exhibitorData);
        }

        $contactData = [];
        if (array_key_exists('contact_person', $data)) $contactData['first_name'] = $data['contact_person'];
        if (array_key_exists('contact_email', $data)) $contactData['email'] = $data['contact_email'];
        if (array_key_exists('contact_number', $data)) $contactData['mobile_number'] = $data['contact_number'];

        if (!empty($contactData)) {
            $exists = $db->table('exhibitor_contact_persons')->where('exhibitor_id', $exhibitorId)->get()->getRow();
            if ($exists) {
                $db->table('exhibitor_contact_persons')->where('exhibitor_id', $exhibitorId)->update($contactData);
            } else {
                $contactData['exhibitor_id'] = $exhibitorId;
                $db->table('exhibitor_contact_persons')->insert($contactData);
            }
        }

        $db->transComplete();
        return $db->transStatus();
    }
}
