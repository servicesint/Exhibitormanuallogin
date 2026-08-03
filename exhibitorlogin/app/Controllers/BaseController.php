<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\ExhibitorModel;

abstract class BaseController extends Controller
{
    protected $helpers = ['custom', 'encryption'];
    protected $session;
    protected $db;
    protected $profile;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
        $this->db      = \Config\Database::connect();

        $this->profile = null;

        if ($this->session->get('logged_in')) {
            $exhibitor_id = $this->session->get('exhibitor_id');

            if ($exhibitor_id) {
                $model = new ExhibitorModel();
                $this->profile = $model->getProfile($exhibitor_id);
            }
        }
    }

    protected function render($view, $data = [])
    {
        $data['profile'] = $this->profile;
        return view($view, $data);
    }
}
