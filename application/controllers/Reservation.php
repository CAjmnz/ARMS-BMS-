<?php defined('BASEPATH') or exit('No direct script access allowed');

class Reservation extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Reservation_model');
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
        $data['title']      = 'Reservations - ARMS-BMS';
        $data['page_label'] = 'Reservations';
        $data['borrowers']  = $this->Borrower_model->get_all();
        $data['items']      = $this->Item_model->get_all();

        $this->load->view('reservation/index', $data);
    }

    // AJAX list for server-side DataTables
    public function ajax_list()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';
        $order     = $this->input->post('order');
        $order_col = $order[0]['column'] ?? 6;
        $order_dir = $order[0]['dir']    ?? 'asc';

        $filters = [
            'reservation_status' => $this->input->post('reservation_status'),
            'date_from'          => $this->input->post('date_from'),
            'date_to'            => $this->input->post('date_to'),
        ];

        $total    = $this->Reservation_model->count_total();
        $filtered = $this->Reservation_model->count_filtered($search, $filters);
        $rows     = $this->Reservation_model->get_datatables($length, $start, $search, $order_col, $order_dir, $filters);

        $data = [];
        $i    = (int) $start + 1;

        foreach ($rows as $row) {

        switch ($row->reservation_status) {
            case 'pending':
                $badge = '<span class="badge badge-warning">Pending</span>';
                break;
            case 'approved':
                $badge = '<span class="badge badge-info">Approved</span>';
                break;
            case 'released':
                $badge = '<span class="badge badge-success">Released</span>';
                break;
            case 'rejected':
                $badge = '<span class="badge badge-danger">Rejected</span>';
                break;
            default:
                $badge = '<span class="badge badge-light">' . ucfirst($row->reservation_status) . '</span>';
        }
    
        $name_parts = preg_split('/[\s,]+/', trim($row->borrower_name ?? ''));
        $initials = '';
        foreach (array_slice($name_parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        $initials = $initials ?: '?';
    
        $photo_url = !empty($row->borrower_photo)
            ? base_url('user/photo_proxy?path=' . urlencode($row->borrower_photo))
            : null;
    
        if ($photo_url) {
            $avatar_html = '<img src="' . $photo_url . '" alt="Photo"
                style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid #e3e6f0; flex-shrink:0;"
                onerror="this.outerHTML=\'<div style=&quot;width:32px;height:32px;border-radius:50%;background:#2563B8;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0;&quot;>' . $initials . '</div>\'">';
        } else {
            $avatar_html = '<div style="width:32px; height:32px; border-radius:50%; background:#2563B8; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; flex-shrink:0;">' . $initials . '</div>';
        }
    
        $borrower_cell = '
            <div style="display:flex; align-items:center; gap:10px;">
                ' . $avatar_html . '
                <span>' . htmlspecialchars($row->borrower_name ?? '-') . '</span>
            </div>';
    
        $action = '
        <div class="dropdown">
            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <div class="dropdown-menu">';
    
        if ($row->reservation_status === 'pending') {
            $action .= '
                <button class="dropdown-item btnApprove" data-id="' . encode_id($row->reservation_id) . '">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button class="dropdown-item btnReject" data-id="' . encode_id($row->reservation_id) . '">
                    <i class="fas fa-times"></i> Reject
                </button>';
        } elseif ($row->reservation_status === 'approved') {
            $action .= '
                <button class="dropdown-item btnRelease" data-id="' . encode_id($row->reservation_id) . '">
                    <i class="fas fa-hand-holding"></i> Release
                </button>';
        }
    
        $action .= '
            </div>
        </div>';
    
        $data[] = [
            $i++,
            'RES-' . str_pad($row->reservation_id, 5, '0', STR_PAD_LEFT),
            htmlspecialchars($row->borrower_employee_id ?? '-'),
            $borrower_cell,
            htmlspecialchars($row->borrower_position ?? '-'),
            htmlspecialchars($row->item_name) . ' #' . $row->unit_no,
            htmlspecialchars($row->category ?? '-'),
            1,
            $row->reservation_date ? date('M d, Y h:i A', strtotime($row->reservation_date)) : '-',
            $row->due_date ? date('M d, Y h:i A', strtotime($row->due_date)) : '-',
            htmlspecialchars($row->purpose ?? '-'),
            $badge,
            htmlspecialchars($row->reserved_by_name ?? '-'),
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
        $units = $this->Reservation_model->get_available_units($item_id);
        echo json_encode(['success' => true, 'units' => $units]);
    }

    // Create a new reservation
    public function store()
    {
        if ($this->input->method() !== 'post') {
            redirect('reservation');
        }

        $borrower_employee_id = $this->input->post('borrower_employee_id');
        $borrower_name        = $this->input->post('borrower_name');
        $borrower_position    = $this->input->post('borrower_position');
        $borrower_dept        = $this->input->post('borrower_dept');
        $borrower_photo       = $this->input->post('borrower_photo');
        $unit_ids             = $this->input->post('unit_ids');
        $purpose               = trim($this->input->post('purpose'));
        $reservation_date       = $this->input->post('reservation_date');
        $due_date                = $this->input->post('due_date');

        if (empty($borrower_employee_id) || empty($borrower_name) || empty($unit_ids) || !is_array($unit_ids) || empty($reservation_date) || empty($due_date)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
            return;
        }

        if (strtotime($reservation_date) < time()) {
            echo json_encode(['success' => false, 'message' => 'Reservation date cannot be in the past.']);
            return;
        }

        if (strtotime($due_date) < strtotime($reservation_date)) {
            echo json_encode(['success' => false, 'message' => 'Return date cannot be earlier than the reservation date.']);
            return;
        }

        $header = [
            'borrower_employee_id' => $borrower_employee_id,
            'borrower_name'         => $borrower_name,
            'borrower_position'     => $borrower_position,
            'borrower_dept'         => $borrower_dept,
            'borrower_photo'        => $borrower_photo,
            'requested_by'           => $this->session->userdata('user_id'),
            'purpose'                => $purpose ?: null,
            'status'                 => 'pending',
            'reservation_date'       => date('Y-m-d H:i:s', strtotime($reservation_date)),
            'due_date'                => date('Y-m-d H:i:s', strtotime($due_date)),
        ];

        $reservation_id = $this->Reservation_model->create_reservation($header, $unit_ids);

        if ($reservation_id) {
            $this->load->model('Itemized_model');
            foreach ($unit_ids as $unit_id) {
                $unit = $this->Itemized_model->get_by_id($unit_id);
                if ($unit) {
                    $item = $this->Item_model->get_by_id($unit->item_id);
                    if ($item) {
                        $this->Item_model->update($unit->item_id, [
                            'available_quantity' => max(0, $item->available_quantity - 1),
                        ]);
                    }
                }
            }
            echo json_encode(['success' => true, 'message' => count($unit_ids) . ' unit(s) reserved successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create reservation.']);
        }
    }

    public function approve($id)
    {
        $decoded_id = decode_id($id);
        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $this->Reservation_model->approve($decoded_id, $this->session->userdata('user_id'));
        echo json_encode(['success' => true, 'message' => 'Reservation approved.']);
    }

    public function reject($id)
    {
        $decoded_id = decode_id($id);
        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $items = $this->Reservation_model->get_items_for_reservation($decoded_id);

        $this->load->model('Itemized_model');
        foreach ($items as $ri) {
            $unit = $this->Itemized_model->get_by_id($ri->unit_id);
            if ($unit) {
                $this->Itemized_model->update($ri->unit_id, ['status' => 'available']);

                $item = $this->Item_model->get_by_id($unit->item_id);
                if ($item) {
                    $this->Item_model->update($unit->item_id, [
                        'available_quantity' => $item->available_quantity + 1,
                    ]);
                }
            }
        }

        $this->Reservation_model->reject($decoded_id);
        echo json_encode(['success' => true, 'message' => 'Reservation rejected and units released.']);
    }

    // Convert an approved reservation into an actual borrowing
    public function release($id)
{
    $decoded_id = decode_id($id);
    if ($decoded_id === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        return;
    }

    $reservation = $this->db->get_where('reservations', ['id' => $decoded_id])->row();
    if (!$reservation) {
        echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
        return;
    }

    $items = $this->Reservation_model->get_items_for_reservation($decoded_id);
    if (empty($items)) {
        echo json_encode(['success' => false, 'message' => 'No units to release.']);
        return;
    }

    $unit_ids = array_column($items, 'unit_id');

    $this->load->model('Borrowing_model');
    $this->load->model('Itemized_model');

    $header = [
        'borrower_employee_id' => $reservation->borrower_employee_id,
        'borrower_name'         => $reservation->borrower_name,
        'borrower_position'     => $reservation->borrower_position,
        'borrower_dept'         => $reservation->borrower_dept,
        'borrower_photo'        => $reservation->borrower_photo,
        'released_by'    => $this->session->userdata('user_id'),
        'purpose'        => $reservation->purpose,
        'status'         => 'released',
        'date_released'  => date('Y-m-d H:i:s'),
        'due_date'       => $reservation->due_date,
    ];

    $borrowing_id = $this->Borrowing_model->create_borrowing($header, $unit_ids);

    if ($borrowing_id) {
        $this->Reservation_model->mark_items_released($decoded_id);
        $this->Reservation_model->mark_reservation_released($decoded_id);

        foreach ($unit_ids as $unit_id) {
            $unit = $this->Itemized_model->get_by_id($unit_id);
            if ($unit) {
                $item = $this->Item_model->get_by_id($unit->item_id);
                if ($item) {
                    $this->Item_model->update($unit->item_id, [
                        'borrowed_quantity' => $item->borrowed_quantity + 1,
                    ]);
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Reservation released as a borrowing.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to release reservation.']);
    }
}
}
