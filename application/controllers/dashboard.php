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
    $data['summary']    = $this->Dashboard_model->get_summary();
    // 'activity' no longer needed here — the table now loads via AJAX

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function activity_ajax_list()
{
    $draw      = $this->input->post('draw');
    $start     = $this->input->post('start');
    $length    = $this->input->post('length');
    $search    = $this->input->post('search')['value'] ?? '';
    $order     = $this->input->post('order');
    $order_col = $order[0]['column'] ?? 0;
    $order_dir = $order[0]['dir']    ?? 'desc';

    $total    = $this->Dashboard_model->count_activity_total();
    $filtered = $this->Dashboard_model->count_activity_filtered($search);
    $rows     = $this->Dashboard_model->get_activity_datatables($length, $start, $search, $order_col, $order_dir);

    $labels = [
        'borrowed' => '<span class="badge badge-warning">Borrowed</span>',
        'returned' => '<span class="badge badge-primary">Returned</span>',
        'damaged'  => '<span class="badge badge-danger">Damaged</span>',
        'lost'     => '<span class="badge badge-dark">Lost</span>',
    ];

    $data = [];
    foreach ($rows as $row) {
        $data[] = [
            date('M d, Y h:i A', strtotime($row->action_date)),
            $labels[$row->action_type] ?? ucfirst($row->action_type),
            htmlspecialchars($row->borrower_name ?? '-'),
            htmlspecialchars($row->item_name) . ' #' . $row->unit_no,
        ];
    }

    echo json_encode([
        'draw'            => (int) $draw,
        'recordsTotal'    => $total,
        'recordsFiltered' => $filtered,
        'data'            => $data,
    ]);
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
