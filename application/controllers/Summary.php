<?php defined('BASEPATH') or exit('No direct script access allowed');

class Summary extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function items()
    {
        $data['title']      = 'Total Items - ARMS-BMS';
        $data['page_label'] = 'Summary — Total Items';
        $this->load->view('summary/items', $data);
    }

    public function units()
    {
        $data['title']      = 'Total Units - ARMS-BMS';
        $data['page_label'] = 'Summary — Total Units';
        $this->load->view('summary/units', $data);
    }

    public function borrowed()
    {
        $data['title']      = 'Currently Borrowed - ARMS-BMS';
        $data['page_label'] = 'Summary — Currently Borrowed';
        $this->load->view('summary/borrowed', $data);
    }

    public function overdue()
    {
        $data['title']      = 'Overdue Items - ARMS-BMS';
        $data['page_label'] = 'Summary — Overdue';
        $this->load->view('summary/overdue', $data);
    }
}