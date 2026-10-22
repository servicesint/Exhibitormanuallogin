<?php

namespace App\Controllers;

/**
 * Exhibitor dashboard pages. Every page loads its data from the external API
 * (API_BASE_URL) in the browser; these actions only render the views.
 * Access is guarded by App\Filters\AuthFilter.
 */
class DashboardController extends BaseController
{
    public function exhibitor_dashboard()
    {
        $payload    = decode_api_token(current_api_token());
        $subEventId = (int) ($payload['sub_event_id'] ?? 0);

        if (!$subEventId) {
            return redirect()->to('login');
        }

        $template = $this->db->query("
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
            LEFT JOIN company_sub_events AS cse ON cse.id = ms.sub_event_id
            LEFT JOIN company_events AS ce ON ce.id = cse.event_id
            LEFT JOIN companies AS c ON c.id = cse.company_id
            LEFT JOIN exhibitors AS e ON e.sub_event_id = cse.id
            WHERE ms.sub_event_id = ?
            LIMIT 1
        ", [$subEventId])->getRowArray();

        if (!$template) {
            return redirect()->to('login');
        }

        $uploadBaseUrl = rtrim((string) env('UPLOAD_BASE_URL', ''), '/');
        $companyLogo   = !empty($template['company_logo'])
            ? $uploadBaseUrl . '/' . ltrim($template['company_logo'], '/')
            : '';

        $placeholders = [
            '{{company_name}}'      => $template['company_name'] ?? '',
            '{{company_logo}}'      => $companyLogo,
            '{{event_name}}'        => $template['event_name'] ?? '',
            '{{sub_event_name}}'    => $template['sub_event_name'] ?? '',
            '{{venue}}'             => $template['venue'] ?? '',
            '{{venue_city}}'        => $template['venue_city'] ?? '',
            '{{full_date}}'         => $this->formatEventDate($template),
            '{{exhibitor_company}}' => $template['organisation_name'] ?? '',
            '{{stall_number}}'      => $template['stall_number'] ?? '',
            '{{date}}'              => date('jS M Y'),
        ];

        $welcomeNote = strtr($template['manual_welcome_note'] ?? '', $placeholders);

        return view('dashboard', ['welcome_note' => $welcomeNote]);
    }

    /**
     * full_date if set, otherwise built from start/end date,
     * e.g. "16TH - 17TH April 2027" or "30TH March - 2ND April 2027".
     */
    private function formatEventDate(array $template): string
    {
        if (!empty($template['full_date']) || empty($template['start_date'])) {
            return $template['full_date'] ?? '';
        }

        $start = new \DateTime($template['start_date']);

        if (empty($template['end_date']) || $template['start_date'] == $template['end_date']) {
            return $start->format('jS F Y');
        }

        $end = new \DateTime($template['end_date']);

        if ($start->format('F Y') === $end->format('F Y')) {
            return strtoupper($start->format('jS')) . ' - ' . strtoupper($end->format('jS')) . ' ' . $end->format('F Y');
        }

        return strtoupper($start->format('jS')) . ' ' . $start->format('F') . ' - '
            . strtoupper($end->format('jS')) . ' ' . $end->format('F Y');
    }

    public function index(): string
    {
        return view('exhibitor/profile');
    }

    public function casual_gst(): string
    {
        return view('exhibitor/casual-gst');
    }

    public function exhibitor_badge(): string
    {
        return view('exhibitor/exhibitor-badge');
    }

    public function fascia(): string
    {
        return view('exhibitor/fascia');
    }

    public function additional_furniture(): string
    {
        return view('exhibitor/additional-furniture');
    }

    public function success(): string
    {
        return view('exhibitor/payment-success');
    }

    public function failed(): string
    {
        return view('exhibitor/payment-failed');
    }

    public function visitor_invitation(): string
    {
        return view('exhibitor/visitor_invitation');
    }

    public function guidelines(): string
    {
        return view('exhibitor/guideline-page');
    }

    public function reference_image(): string
    {
        return view('exhibitor/reference-image');
    }

    public function important_information(): string
    {
        return $this->page('important-information');
    }

    public function accommodation(): string
    {
        return $this->page('accommodation');
    }

    public function empanelled_sand(): string
    {
        return $this->page('empanelled-sand');
    }

    public function contact_details(): string
    {
        return $this->page('contact-details');
    }

    public function other_information(): string
    {
        return $this->page('other-information');
    }

    public function raw_space(): string
    {
        return $this->page('raw-space');
    }

    public function generateParticipationLetter(): string
    {
        return $this->page('participation-letter');
    }

    public function generateExitPermit(): string
    {
        return $this->page('exit-permit');
    }

    /**
     * Renders exhibitor/<name>, accepting either hyphen or underscore file names.
     * Missing views give a 404 instead of a fatal error.
     */
    private function page(string $name): string
    {
        foreach ([$name, str_replace('-', '_', $name)] as $file) {
            if (is_file(APPPATH . 'Views/exhibitor/' . $file . '.php')) {
                return view('exhibitor/' . $file);
            }
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
}
