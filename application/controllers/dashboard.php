<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('Asia/Manila');

class Dashboard extends CI_Controller
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
        $data['title']      = 'Dashboard - ARMS-BMS';
        $data['page_label'] = 'Dashboard';

        $data['summary'] = $this->Dashboard_model
            ->get_summary();

        $data['activity'] = $this->Dashboard_model
            ->get_recent_activity();

        $data['most_borrowed_items'] = $this->Dashboard_model
            ->get_most_borrowed_items(5);

        $data['due_today'] = $this->Dashboard_model
            ->get_due_today(8);

        $data['low_stock_items'] = $this->Dashboard_model
            ->get_low_stock_items(8);

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function chart_categories()
    {
        $categories = $this->Dashboard_model
            ->get_category_breakdown();

        $labels = array();
        $values = array();

        foreach ($categories as $row) {
            $labels[] = $row->category;
            $values[] = (int) $row->total_quantity;
        }

        $this->_json_response(array(
            'labels' => $labels,
            'values' => $values
        ));
    }

    public function chart_trend()
    {
        $trend = $this->Dashboard_model
            ->get_borrowing_trend(12);

        $labels = array();
        $values = array();

        foreach ($trend as $month => $count) {
            $labels[] = date(
                'M Y',
                strtotime($month . '-01')
            );

            $values[] = (int) $count;
        }

        $this->_json_response(array(
            'labels' => $labels,
            'values' => $values
        ));
    }

    public function chart_most_borrowed()
    {
        $items = $this->Dashboard_model
            ->get_most_borrowed_items(5);

        $labels = array();
        $values = array();

        foreach ($items as $item) {
            $labels[] = $item->item_name;
            $values[] = (int) $item->borrow_count;
        }

        $this->_json_response(array(
            'labels' => $labels,
            'values' => $values
        ));
    }

    private function _json_response($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
