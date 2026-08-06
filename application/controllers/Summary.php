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
        $this->load->model('Item_model');
        $this->load->model('Itemized_model');

        $data['title']      = 'Total Items - ARMS-BMS';
        $data['page_label'] = 'Summary — Total Items';
        $data['total_items'] = $this->Item_model->count_total();
        $data['status_breakdown'] = $this->Item_model->get_status_breakdown();

        $this->load->view('summary/items', $data);
    }

    public function units()
    {
        $this->load->model('Itemized_model');

        $data['title']      = 'Total Units - ARMS-BMS';
        $data['page_label'] = 'Summary — Total Units';
        $data['total_units'] = $this->Itemized_model->count_total();
        $data['status_breakdown'] = $this->Itemized_model->get_status_breakdown();
        
        $this->load->view('summary/units', $data);
    }

    public function borrowed()
    {
        $this->load->model('Borrowing_model');

        $data['title']      = 'Currently Borrowed - ARMS-BMS';
        $data['page_label'] = 'Summary — Currently Borrowed';
        $data['total_borrowed'] = $this->db->where('item_status', 'borrowed')->count_all_results('borrowing_items');
        $data['status_breakdown'] = $this->Borrowing_model->get_status_breakdown();

        $this->load->view('summary/borrowed', $data);
    }
    public function overdue()
    {
        $this->load->model('Borrowing_model');

        $overdue_list = $this->Borrowing_model->get_overdue_list();

        // Bucket by days late
        $buckets = ['1-3 days' => 0, '4-7 days' => 0, '8-14 days' => 0, '15+ days' => 0];
        $by_category = [];

        foreach ($overdue_list as $row) {
            $days = floor((time() - strtotime($row->due_date)) / 86400);
            $row->days_overdue = $days; // attach for the view

            if ($days <= 3) {
                $buckets['1-3 days']++;
            } elseif ($days <= 7) {
                $buckets['4-7 days']++;
            } elseif ($days <= 14) {
                $buckets['8-14 days']++;
            } else {
                $buckets['15+ days']++;
            }

            $cat = $row->category ?: 'Uncategorized';
            $by_category[$cat] = ($by_category[$cat] ?? 0) + 1;
        }

        $data['title']         = 'Overdue Items - ARMS-BMS';
        $data['page_label']    = 'Summary — Overdue';
        $data['overdue_list']  = $overdue_list;
        $data['total_overdue'] = count($overdue_list);
        $data['days_buckets']  = $buckets;
        $data['by_category']   = $by_category;

        $this->load->view('summary/overdue', $data);
    }

    public function available()
    {
        $this->load->model('Item_model');

        $data['title']              = 'Available Items - ARMS-BMS';
        $data['page_label']         = 'Summary — Available Items';
        $data['items']               = $this->Item_model->get_all();
        $data['total_available']     = $this->Item_model->get_total_available();
        $data['category_availability'] = $this->Item_model->get_category_availability();
        $data['status_breakdown']    = $this->Item_model->get_status_breakdown();

        $this->load->view('summary/available', $data);
    }
}
