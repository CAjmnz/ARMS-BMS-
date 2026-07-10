<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Borrowing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Borrowing_model');
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    /**
     * Borrowing Monitoring Page
     */
    public function index()
    {
        $data = [
            'title'      => 'Borrowing - ARMS-BMS',
            'page_label' => 'Borrowing'
        ];

        $this->load->view('borrowing/index', $data);
    }

    /**
     * Server-side DataTables
     */
    public function ajax_list()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';

        $order     = $this->input->post('order');
        $order_col = $order[0]['column'] ?? 0;
        $order_dir = $order[0]['dir'] ?? 'desc';

        $filters = [
            'status'    => $this->input->post('status'),
            'date_from' => $this->input->post('date_from'),
            'date_to'   => $this->input->post('date_to')
        ];

        $total = $this->Borrowing_model->count_total();

        $filtered = $this->Borrowing_model->count_filtered(
            $search,
            $filters
        );

        $borrowings = $this->Borrowing_model->get_datatables(
            $length,
            $start,
            $search,
            $order_col,
            $order_dir,
            $filters
        );

        foreach ($borrowings as $row) {

        switch ($row->status) {
    
            case 'Pending':
                $badge = '<span class="badge badge-secondary">Pending</span>';
                break;
    
            case 'Released':
                $badge = '<span class="badge badge-warning">Released</span>';
                break;
    
            case 'Returned':
                $badge = '<span class="badge badge-success">Returned</span>';
                break;
    
            case 'Overdue':
                $badge = '<span class="badge badge-danger">Overdue</span>';
                break;
    
            case 'Cancelled':
                $badge = '<span class="badge badge-dark">Cancelled</span>';
                break;
    
            default:
                $badge = '<span class="badge badge-light">'.$row->status.'</span>';
        }
    
        $action = '
        <div class="dropdown">
            <button class="btn btn-secondary btn-sm dropdown-toggle"
                data-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
    
            <div class="dropdown-menu">
    
                <button class="dropdown-item btnView"
                    data-id="'.$row->id.'">
                    <i class="fas fa-eye"></i> View
                </button>
    
                <button class="dropdown-item btnReturn"
                    data-id="'.$row->id.'">
                    <i class="fas fa-undo"></i> Return
                </button>
    
            </div>
        </div>';
    
        $data[] = [
    
            $i++,
    
            htmlspecialchars($row->borrower_id),
    
            htmlspecialchars($row->borrower_name),
    
            htmlspecialchars($row->item_name . ' #'.$row->unit_no),
    
            htmlspecialchars($row->category),
    
            1,
    
            ucfirst($row->condition_before),
    
            date('M d, Y', strtotime($row->borrow_date)),
    
            date('M d, Y', strtotime($row->due_date)),
    
            $badge,
    
            htmlspecialchars($row->released_by),
    
            $action
    
        ];
    }

        echo json_encode([
            'draw'            => (int)$draw,
            'recordsTotal'    => (int)$total,
            'recordsFiltered' => (int)$filtered,
            'data'            => $data
        ]);
    }

    /**
     * Create Borrowing
     */
    public function store()
    {

    }

    /**
     * View Borrowing
     */
    public function get($id)
    {

    }

    /**
     * Update Borrowing
     */
    public function update($id)
    {

    }

    /**
     * Delete Borrowing
     */
    public function delete($id)
    {

    }
}