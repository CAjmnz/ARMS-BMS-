<?php defined('BASEPATH') or exit('No direct script access allowed');

class Borrowing extends CI_Controller
{

public function __construct()
{
    parent::__construct();
    $this->load->model('Borrowing_model');
    $this->load->model('Borrower_model');
    $this->load->model('Item_model');
    $this->load->library('session');
    $this->load->helper('url');

    if (!$this->session->userdata('logged_in')) {
        redirect('auth');
    }
}

public function index()
{
    $data['title']      = 'Borrowing - ARMS-BMS';
    $data['page_label'] = 'Borrowing';
    $data['borrowers']  = $this->Borrower_model->get_all();
    $data['items']      = $this->Item_model->get_all();


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

        if ($row->item_status === 'borrowed' && $row->due_date && strtotime($row->due_date) < time()) {
            $badge = '<span class="badge badge-danger">Overdue</span>';
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
                <button class="dropdown-item btnReturn" data-id="' . encode_id($row->id) . '">
                    <i class="fas fa-undo"></i> Mark Returned
                </button>
                <button class="dropdown-item btnDelete"
                    data-id="' . encode_id($row->id) . '"
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

// AJAX — get available units for an item (called when item dropdown changes)
public function get_available_units($item_id)
{
    $units = $this->Borrowing_model->get_available_units($item_id);
    echo json_encode(['success' => true, 'units' => $units]);
}

// Create a new borrowing transaction
public function store()
{
    if ($this->input->method() !== 'post') {
        redirect('borrowing');
    }

    $borrower_id = $this->input->post('borrower_id');
    $unit_ids    = $this->input->post('unit_ids');
    $purpose     = trim($this->input->post('purpose'));
    $due_date    = $this->input->post('due_date');

    if (empty($borrower_id)) {
        echo json_encode(['success' => false, 'message' => 'Please select a borrower.']);
        return;
    }

    if (empty($unit_ids) || !is_array($unit_ids)) {
        echo json_encode(['success' => false, 'message' => 'Please select at least one unit.']);
        return;
    }

    if (empty($due_date)) {
        echo json_encode(['success' => false, 'message' => 'Please set a due date.']);
        return;
    }

    $header = [
        'borrower_id'    => $borrower_id,
        'released_by'    => $this->session->userdata('user_id'),
        'purpose'        => $purpose ?: null,
        'status'         => 'released',
        'date_released'  => date('Y-m-d H:i:s'),
        'due_date'       => date('Y-m-d H:i:s', strtotime($due_date)),
    ];

    $borrowing_id = $this->Borrowing_model->create_borrowing($header, $unit_ids);

    if ($borrowing_id) {
        // Sync item-level available/borrowed counts
        $this->load->model('Itemized_model');
        foreach ($unit_ids as $unit_id) {
            $unit = $this->Itemized_model->get_by_id($unit_id);
            if ($unit) {
                $item = $this->Item_model->get_by_id($unit->item_id);
                if ($item) {
                    $this->Item_model->update($unit->item_id, [
                        'available_quantity' => max(0, $item->available_quantity - 1),
                        'borrowed_quantity'  => $item->borrowed_quantity + 1,
                    ]);
                   
                }
            }
        }

        echo json_encode(['success' => true, 'message' => count($unit_ids) . ' unit(s) released successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create borrowing record.']);
    }
}

public function get($encoded_id)
{
    $id = decode_id($encoded_id);

    if($id === null ){
        echo json_encode(['success' => false,'message'=> 'Invalid request.']);
    return;
}
    $unit = $this->itemized_model->get_by_id($id);
    if($unit){
        echo json_encode(['success' => true,'item'=> $unit]);
    } else {
        echo json_encode(['success' => false,'message' => 'Unit not found.']);
    }
}
// Get a single borrowing_item's details (for the Mark Returned modal)
public function get_item($id)
{
    $decoded_id = decode_id($id);

    if ($decoded_id === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        return;
    }

    $item = $this->Borrowing_model->get_item_by_id($decoded_id);
    if ($item) {
        echo json_encode(['success' => true, 'item' => $item]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Record not found.']);
    }
}

// Mark a unit as returned/damaged/lost
public function mark_returned($id)
{
    $decoded_id = decode_id($id);

    if ($decoded_id === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        return;
    }

    if ($this->input->method() !== 'post') {
        redirect('borrowing');
    }

    $condition_after = $this->input->post('condition_after');
    $item_status     = $this->input->post('item_status');
    $remarks         = trim($this->input->post('remarks'));

    $borrowing_item = $this->Borrowing_model->get_item_by_id($decoded_id);

    if (!$borrowing_item) {
        echo json_encode(['success' => false, 'message' => 'Record not found.']);
        return;
    }

    // Update the borrowing_items row
    $this->Borrowing_model->mark_returned($decoded_id, [
        'condition_after' => $condition_after,
        'item_status'     => $item_status,
        'date_returned'   => date('Y-m-d H:i:s'),
        'received_by'     => $this->session->userdata('user_id'),
        'remarks'         => $remarks ?: null,
    ]);

    // Update the itemized unit itself
    $this->load->model('Itemized_model');
    $unit_status = ($item_status === 'returned') ? 'available' : $item_status;

    $this->Itemized_model->update($borrowing_item->unit_id, [
        'status'         => $unit_status,
        'item_condition' => ($item_status === 'returned') ? $condition_after : 'needs repair',
    ]);

    // Sync parent item quantities
    $this->load->model('Item_model');
    $unit = $this->Itemized_model->get_by_id($borrowing_item->unit_id);
    if ($unit) {
        $item = $this->Item_model->get_by_id($unit->item_id);
        if ($item) {
            $new_available = $item->available_quantity;
            $new_borrowed  = max(0, $item->borrowed_quantity - 1);

            if ($item_status === 'returned') {
                $new_available = $item->available_quantity + 1;
            }

            $this->Item_model->update($unit->item_id, [
                'available_quantity' => $new_available,
                'borrowed_quantity'  => $new_borrowed,
            ]);
        }
    }

    // If every item in this transaction is back, close the borrowing
    if ($this->Borrowing_model->all_items_returned($borrowing_item->borrowing_id)) {
        $this->Borrowing_model->close_borrowing($borrowing_item->borrowing_id);
    }

    echo json_encode(['success' => true, 'message' => 'Unit marked as ' . $item_status . '.']);
}
}