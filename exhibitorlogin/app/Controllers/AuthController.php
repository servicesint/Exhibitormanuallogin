<?php

namespace App\Controllers;

use App\Models\ExhibitorModel;

/**
 * Event selection and login page. The OTP login itself is handled by the
 * external API (API_BASE_URL/auth/v1/*) from the login view.
 */
class AuthController extends BaseController
{
    private const DEFAULT_EVENT_ID = 1;

    public function generate_main_event_code()
    {
        return redirect()->to('event/' . encryptData(self::DEFAULT_EVENT_ID));
    }

    public function index($enc_id = null)
    {
        $decrypted_id = decryptData($enc_id);
        if (!$decrypted_id) {
            return redirect()->to('/comingSoon')->with('fail', 'Invalid request');
        }
        $subevents = (new ExhibitorModel())->getActiveSubEvents($decrypted_id);
        if (empty($subevents)) {
            return redirect()->to('/comingSoon')->with('fail', 'No active events found');
        }
        $this->session->set('enc_sub_event_id', $enc_id);
        if (count($subevents) === 1) {
            return redirect()->to('login/' . encryptData($subevents[0]->sub_event_id));
        }
        return view('event', ['subevents' => $subevents]);
    }

    public function comingSoon()
    {
        return view('coming_soon');
    }

    public function exlogin($encrypted_sub_event_id = null)
    {
        // The URL segment is always an encrypted sub_event id
        $encrypted_sub_event_id ??= $this->session->get('login_enc_id');
        if ($encrypted_sub_event_id) {
            $sub_event_id = decryptData($encrypted_sub_event_id);
            if (!$sub_event_id) {
                return redirect()->to('/comingSoon')->with('fail', 'Invalid event data');
            }
            $subEvent = $this->db->table('company_sub_events')
                ->where('id', $sub_event_id)
                ->get(1)
                ->getRowArray();
        } else {
            $subEvent = $this->db->table('company_sub_events')
                ->where('event_id', self::DEFAULT_EVENT_ID)
                ->where('end_date >=', date('Y-m-d'))
                ->orderBy('start_date', 'ASC')
                ->get(1)
                ->getRowArray();
            $encrypted_sub_event_id = $subEvent ? encryptData($subEvent['id']) : null;
        }
        if (!$subEvent) {
            return redirect()->to('/comingSoon')->with('fail', 'No active events found');
        }
        $this->session->set([
            'sub_event_id' => $subEvent['id'],
            'login_enc_id' => $encrypted_sub_event_id,
        ]);
        // Survives session destroy, so logout still knows which event website to return to
        $this->response->setCookie('exh_sub_event', $encrypted_sub_event_id, 2592000); // 30 days
        $event = $this->db->table('company_events')
            ->where('id', $subEvent['event_id'])
            ->get(1)
            ->getRowArray() ?? [];
        $event['event_id'] = (int) $subEvent['event_id'];
        $subEvent['sub_event_id'] = (int) $subEvent['id'];
        return view('login', [
            'enc_sub_event_id' => $encrypted_sub_event_id,
            'event'            => $event,
            'subEvent'         => $subEvent,
        ]);
    }

    public function logout()
    {
        helper('cookie');
        $subEventId = (int) $this->session->get('sub_event_id');
        if (!$subEventId && ($cookie = get_cookie('exh_sub_event'))) {
            $subEventId = (int) decryptData($cookie);
        }
        $this->session->destroy();

        // Send the exhibitor back to the event's main website
        $event = $subEventId ? $this->db->table('company_sub_events cse')
            ->select('ce.*')
            ->join('company_events ce', 'ce.id = cse.event_id')
            ->where('cse.id', $subEventId)
            ->get(1)
            ->getRowArray() : null;

        foreach (['website', 'website_url', 'event_website', 'event_url', 'site_url'] as $column) {
            $url = trim((string) ($event[$column] ?? ''));
            if ($url !== '') {
                // "www.site.com" would be treated as an app path; make it absolute
                if (!preg_match('#^https?://#i', $url)) {
                    $url = 'https://' . ltrim($url, '/');
                }
                return redirect()->to($url);
            }
        }

        $fallback = (string) env('EVENT_WEBSITE_URL', '');
        return redirect()->to($fallback !== '' ? $fallback : base_url('generate'));
    }

    public function error404()
    {
        return response()->setStatusCode(404)->setBody(
            view('errors/custom_404', ['enc_id' => $this->session->get('enc_sub_event_id')])
        );
    }
}
