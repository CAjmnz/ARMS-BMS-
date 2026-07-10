<?php defined('BASEPATH') or exit('No direct script access allowed');

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

    // Show borrowing monitoring page
    public function index()
    {
        $data['title']      = 'Borrowing - ARMS-BMS';
        $data['page_label'] = 'Borrowing';

        $this->load->view('borrowing/index', $data);
    }

    // AJAX list for server-side DataTables
    public function ajax_list()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';
        $order     = $this->input->post('order');
        $order_col = $order[0]['column'] ?? 5;
        $order_dir = $order[0]['dir']    ?? 'desc';

        $filters = [
            'item_status'      => $this->input->post('item_status'),
            'borrowing_status' => $this->input->post('borrowing_status'),
            'date_from'        => $this->input->post('date_from'),
            'date_to'          => $this->input->post('date_to'),
        ];

        $total    = $this->Borrowing_model->count_total();
        $filtered = $this->Borrowing_model->count_filtered($search, $filters);
        $rows     = $this->Borrowing_model->get_datatables($length, $start, $search, $order_col, $order_dir, $filters);

        $data = [];
        $i    = (int) $start + 1;

        foreach ($rows as $row) {

            // Borrowing status badge (per-unit item_status)
            switch ($row->item_status) {
                case 'borrowed':
                    $badge = '<span class="badge badge-warning">Borrowed</span>';
                    break;
                case 'returned':
                    $badge = '<span class="badge badge-primary">Returned</span>';
                    break;
                case 'damaged':
                    $badge = '<span class="badge badge-danger">Damaged</span>';
                    break;
                case 'lost':
                    $badge = '<span class="badge badge-dark">Lost</span>';
                    break;
                default:
                    $badge = '<span class="badge badge-light">' . ucfirst($row->item_status) . '</span>';
            }

            // Flag overdue (borrowed + past due date)
            if ($row->item_status === 'borrowed' && $row->due_date && strtotime($row->due_date) < time()) {
                $badge = '<span class="badge badge-danger">Overdue</span>';
            }

            // Action dropdown
            $action = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item btnView" data-id="' . $row->id . '">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="dropdown-item btnReturn" data-id="' . $row->id . '">
                        <i class="fas fa-undo"></i> Mark Returned
                    </button>
                    <button class="dropdown-item btnDelete"
                        data-id="' . $row->id . '"
                        data-name="' . htmlspecialchars($row->item_name) . ' #' . $row->unit_no . '">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>';

            $data[] = [
                $i++,
                htmlspecialchars($row->id_number ?? '-'),
                htmlspecialchars($row->borrower_name ?? '-'),
                htmlspecialchars($row->item_name) . ' #' . $row->unit_no,
                htmlspecialchars($row->category ?? '-'),
                1,
                ucfirst($row->condition_before),
                $row->date_released ? date('M d, Y h:i A', strtotime($row->date_released)) : '-',
                $row->due_date ? date('M d, Y h:i A', strtotime($row->due_date)) : '-',
                $badge,
                htmlspecialchars($row->released_by_name ?? '-'),
                $action,
            ];
        }

        echo json_encode([
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total,
            'recordsFiltered' => (int) $filtered,
            'data'            => $data,
        ]);
    }
}