<?php defined('BASEPATH') or exit('No direct script access allowed');

class Total_items extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title']      = 'Total_items - ARMS-BMS';
        $data['page_label'] = 'Total_items';
        $data['summary']    = $this->Dashboard_model->get_summary();
        $data['activity']   = $this->Dashboard_model->get_recent_activity();

        $this->load->view('total_items/index', $data);
    }

    // AJAX — chart data for category breakdown
    public function chart_categories()
    {
        $data = $this->Dashboard_model->get_category_breakdown();
        $labels = [];
        $values = [];
        foreach ($data as $row) {
            $labels[] = $row->category;
            $values[] = (int) $row->total_quantity;
        }
        echo json_encode(['labels' => $labels, 'values' => $values]);
    }

    // AJAX — chart data for borrowing trend
    public function chart_trend()
    {
        $trend = $this->Dashboard_model->get_borrowing_trend(7);
        $labels = [];
        $values = [];
        foreach ($trend as $date => $count) {
            $labels[] = date('M d', strtotime($date));
            $values[] = $count;
        }
        echo json_encode(['labels' => $labels, 'values' => $values]);
    }
}