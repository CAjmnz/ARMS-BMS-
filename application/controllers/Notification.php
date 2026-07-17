<?php defined('BASEPATH')or exit ('No direct script access allowed');

class Notification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Notification_model');
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')){
            redirect('auth');
        }
    }
    public function index()
    {
        $data['title'] = 'Notification - ARMS-BMS';
        $data['page_label'] = 'Notifications';
        $data['overdue'] = $this->Notification_model->get_overdue();
        $data['due_soon'] = $this->Notification_model->get_due_soon();

        $this->load->view('notifications/index',$data);

    }

    // AJAX — used by the topbar bell to refresh the count periodically 
    public function get_count()
    {
        $count = $this->Notification_model->get_notification_count();
        echo json_encode(['success' => true,'count' => $count]);
    }
}