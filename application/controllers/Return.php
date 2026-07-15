<?php defined('BASEPATH') or exit('No direct script access allowed');

class Return_c extends CI_Controller
{
    // Note: named Return_c because 'Return' is a reserved PHP keyword and cannot be used as a class name

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Return_model');
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title']      = 'Returns - ARMS-BMS';
        $data['page_label'] = 'Returns';

        $this->load->view('returns/index', $data);
    }

    public function ajax_list()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';
        $order     = $this->input->post('order');
        $order_col = $order[0]['column'] ?? 8;
        $order_dir = $order[0]['dir']    ?? 'desc';

        $filters = [
            'item_status' => $this->input->post('item_status'),
            'date_from'   => $this->input->post('date_from'),
            'date_to'     => $this->input->post('date_to'),
        ];

        $total    = $this->Return_model->count_total();
        $filtered = $this->Return_model->count_filtered($search, $filters);
        $rows     = $this->Return_model->get_datatables($length, $start, $search, $order_col, $order_dir, $filters);

        $data = [];
        $i    = (int) $start + 1;

        foreach ($rows as $row) {

            switch ($row->item_status) {
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

            // Days late calculation
            $days_late = '-';
            if ($row->due_date && $row->date_returned) {
                $due    = strtotime($row->due_date);
                $actual = strtotime($row->date_returned);
                $diff   = floor(($actual - $due) / 86400);
                $days_late = $diff > 0 ? $diff . ' day(s)' : 'On time';
            }

            $action = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item btnView" data-id="' . encode_id($row->id) . '">
                        <i class="fas fa-eye"></i> View
                    </button>
                </div>
            </div>';

            $data[] = [
                $i++,
                'TXN-' . str_pad($row->borrowing_id, 5, '0', STR_PAD_LEFT),
                htmlspecialchars($row->id_number ?? '-'),
                htmlspecialchars($row->borrower_name ?? '-'),
                htmlspecialchars($row->item_name) . ' #' . $row->unit_no,
                htmlspecialchars($row->category ?? '-'),
                1,
                ucfirst($row->condition_after ?? '-'),
                $row->date_released ? date('M d, Y h:i A', strtotime($row->date_released)) : '-',
                $row->due_date ? date('M d, Y h:i A', strtotime($row->due_date)) : '-',
                $row->date_returned ? date('M d, Y h:i A', strtotime($row->date_returned)) : '-',
                $days_late,
                $badge,
                htmlspecialchars($row->received_by_name ?? '-'),
                htmlspecialchars($row->remarks ?? '-'),
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