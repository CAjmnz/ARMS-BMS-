<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Itemized extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Itemized_model');
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    // Show itemized page
    public function index()
    {
        $data['title']      = 'Itemized - ARMS-BMS';
        $data['page_label'] = 'Itemized';

        $this->load->view('itemized/index', $data);
    }

    // Add new unit
    public function store()
    {
        if ($this->input->method() !== 'post') {
            redirect('itemized');
        }

        $data = [
            'item_id'           => $this->input->post('item_id'),
            'unit_no'           => $this->input->post('unit_no'),
            'status'            => $this->input->post('status'),
            'item_condition'    => $this->input->post('item_condition'),
            'item_description'  => trim($this->input->post('item_description')),
        ];

        if ($this->Itemized_model->insert($data)) {
            echo json_encode(['success' => true, 'message' => 'Unit added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add unit.']);
        }
    }

    // Get single unit
    public function get($id)
    {
        $unit = $this->Itemized_model->get_by_id($id);
        if ($unit) {
            echo json_encode(['success' => true, 'item' => $unit]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unit not found.']);
        }
    }

    // Update unit
    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('itemized');
        }

        $data = [
            'status'            => $this->input->post('status'),
            'item_condition'    => $this->input->post('item_condition'),
            'item_description'  => trim($this->input->post('item_description')),
        ];

        if ($this->Itemized_model->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => 'Unit updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update unit.']);
        }
    }

    // Delete unit
    public function delete($id)
    {
        if ($this->Itemized_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Unit deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete unit.']);
        }
    }

    // AJAX list for server-side DataTables
    public function ajax_list()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';
        $order     = $this->input->post('order');
        $order_col = $order[0]['column'] ?? 0;
        $order_dir = $order[0]['dir']    ?? 'asc';

        $filters = [
            'status'         => $this->input->post('status'),
            'item_condition' => $this->input->post('item_condition'),
            'date_from'      => $this->input->post('date_from'),
            'date_to'        => $this->input->post('date_to'),
        ];

        $total    = $this->Itemized_model->count_total();
        $filtered = $this->Itemized_model->count_filtered($search, $filters);
        $units    = $this->Itemized_model->get_datatables($length, $start, $search, $order_col, $order_dir, $filters);

        $data = [];
        $i    = (int) $start + 1;

        foreach ($units as $unit) {

            // Status badge
            switch ($unit->status) {
                case 'available':
                    $badge = '<span class="badge badge-success">Available</span>';
                    break;
                case 'borrowed':
                    $badge = '<span class="badge badge-warning">Borrowed</span>';
                    break;
                case 'reserved':
                    $badge = '<span class="badge badge-info">Reserved</span>';
                    break;
                case 'returned':
                    $badge = '<span class="badge badge-primary">Returned</span>';
                    break;
                case 'overdue':
                    $badge = '<span class="badge badge-danger">Overdue</span>';
                    break;
                case 'missing':
                    $badge = '<span class="badge badge-dark">Missing</span>';
                    break;
                case 'damaged':
                    $badge = '<span class="badge badge-danger">Damaged</span>';
                    break;
                case 'archived':
                    $badge = '<span class="badge badge-secondary">Archived</span>';
                    break;
                case 'under_review':
                    $badge = '<span class="badge badge-info">Under Review</span>';
                    break;
                case 'disposed':
                    $badge = '<span class="badge badge-secondary">Disposed</span>';
                    break;
                default:
                    $badge = '<span class="badge badge-light">' . ucfirst($unit->status) . '</span>';
            }

            // Action dropdown
            $action = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item btnEdit" data-id="' . $unit->id . '">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="dropdown-item btnDelete"
                        data-id="' . $unit->id . '"
                        data-name="' . htmlspecialchars($unit->item_name) . ' #' . $unit->unit_no . '">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>';

            $data[] = [
                $i++,
                htmlspecialchars($unit->item_name),
                $unit->unit_no,
                $badge,
                ucfirst($unit->item_condition),
                htmlspecialchars($unit->item_description ?? '-'),
                date('M d, Y h:i A', strtotime($unit->created_at)),
                date('M d, Y h:i A', strtotime($unit->updated_at)),
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