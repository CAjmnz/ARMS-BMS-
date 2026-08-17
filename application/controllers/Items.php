<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Items extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Item_model');
        $this->load->library('session', 'encryption');
        $this->load->helper('url');



        // CI3 way of checking session (not $_SESSION)
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }
    //Show Items page
    public function index()
    {
        $filters = [
            'status' => $this->input->get('status'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
        ];

        $data['title'] = 'items - ARMS-BMS';
        $data['page_label'] = 'Item Management';
        $data['items'] = $this->Item_model->get_all($filters);

        $this->load->view('items/index', $data);
    }
    //Add new item 
    public function store()
    {
        if ($this->input->method() !== 'post') {
            redirect('items');
        }

        $quantity = (int) $this->input->post('quantity');

        $data = [
            'item_name'          => trim($this->input->post('item_name')),
            'category'           => trim($this->input->post('category')),
            'brand'              => trim($this->input->post('brand')),
            'Model'              => trim($this->input->post('model')),
            'serial_number'      => trim($this->input->post('serial_number')),
            'quantity'           => $quantity,
            'available_quantity' => $quantity,  // all available at start
            'borrowed_quantity'  => 0,
            'status'             => $this->input->post('status'),
            'location'           => trim($this->input->post('location')),
        ];

        // Insert item first
        $this->Item_model->insert($data);
        $item_id = $this->db->insert_id();  // get the new item's ID

        // Auto-generate itemized units
        if ($item_id && $quantity > 0) {
            $this->load->model('Itemized_model');
            $units = [];
            for ($i = 1; $i <= $quantity; $i++) {
                $units[] = [
                    'item_id'          => $item_id,
                    'unit_no'          => $i,
                    'status'           => 'available',
                    'item_condition'   => 'new',
                    'item_description' => trim($this->input->post('item_name')) . ' unit ' . $i,
                ];
            }
            $this->Itemized_model->insert_batch($units);
        }

        echo json_encode(['success' => true, 'message' => 'Item added and ' . $quantity . ' units generated.']);
    }
    //get individual item
    public function get($id)
    {
        $decoded_id = decode_id($id);

        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }
        $item = $this->Item_model->get_by_id($decoded_id);
        if ($item) {
            echo json_encode(['success' => true, 'item' => $item]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found.']);
        }
    }

    // update item
    public function update($id)
    {
        $decoded_id = decode_id($id);

        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        if ($this->input->method() !== 'post') {
            redirect('items');
        }

        $new_quantity = (int) $this->input->post('quantity');

        $data = [
            'item_name'          => trim($this->input->post('item_name')),
            'category'           => trim($this->input->post('category')),
            'brand'              => trim($this->input->post('brand')),
            'Model'              => trim($this->input->post('model')),
            'serial_number'      => trim($this->input->post('serial_number')),
            'quantity'           => $new_quantity,
            'status'             => $this->input->post('status'),
            'location'           => trim($this->input->post('location')),
        ];

        $this->Item_model->update($decoded_id, $data);

        // Sync itemized
        $this->load->model('Itemized_model');
        $current_count = $this->Itemized_model->count_by_item_id($decoded_id);

        if ($new_quantity > $current_count) {
            // Add more units
            $last_unit_no = $this->Itemized_model->get_last_unit_no($decoded_id);
            $item  = $this->Item_model->get_by_id($decoded_id);
            $units = [];

            for ($i = $current_count + 1; $i <= $new_quantity; $i++) {
                $last_unit_no++;
                $units[] = [
                    'item_id'          => $decoded_id,
                    'unit_no'          => $last_unit_no,
                    'status'           => 'available',
                    'item_condition'   => 'new',
                    'item_description' => $item->item_name . ' unit ' . $last_unit_no,
                ];
            }
            $this->Itemized_model->insert_batch($units);
        } elseif ($new_quantity < $current_count) {
            // Remove extra units
            $this->Itemized_model->delete_extra_units($decoded_id, $new_quantity);
        }

        echo json_encode(['success' => true, 'message' => 'Item updated successfully.']);
    }

    //Delete item 
// Delete unit
// Delete item
public function delete($id)
{
    $decoded_id = decode_id($id);

    if ($decoded_id === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        return;
    }

    $item = $this->Item_model->get_by_id($decoded_id);

    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item not found.']);
        return;
    }

    if ($this->Item_model->delete($decoded_id)) {
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete Item.']);
    }
}
    // Logout
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }

    // ajax list
    public function ajax_list()
    {
        // Get DataTables parameters
        $draw     = $this->input->post('draw');
        $start    = $this->input->post('start');
        $length   = $this->input->post('length');
        $search   = $this->input->post('search')['value'] ?? '';
        $order    = $this->input->post('order');
        $order_col = $order[0]['column'] ?? 0;
        $order_dir = $order[0]['dir']    ?? 'asc';

        // Get filters
        $filters = [
            'status'    => $this->input->post('status'),
            'date_from' => $this->input->post('date_from'),
            'date_to'   => $this->input->post('date_to'),
        ];

        // Get counts
        $total    = $this->Item_model->count_total();
        $filtered = $this->Item_model->count_filtered($search, $filters);

        // Get rows
        $items = $this->Item_model->get_datatables($length, $start, $search, $order_col, $order_dir, $filters);

        // Build rows
        $data = [];
        $i    = (int)$start + 1;

        foreach ($items as $item) {

            // Status badge
            if ($item->status === 'available') {
                $badge = '<span class="badge badge-success">Available</span>';
            } elseif ($item->status === 'in-use') {
                $badge = '<span class="badge badge-warning">In Use</span>';
            } else {
                $badge = '<span class="badge badge-danger">Unavailable</span>';
            }

            // Action dropdown
            $action = '
            <div class="dropdown">
                <button class="doc-actions-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item btnEdit" data-id="' . encode_id($item->id) . '">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="dropdown-item btnDelete"
                        data-id="' . encode_id($item->id) . '"
                        data-name="' . htmlspecialchars($item->item_name) . '">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>';

            $data[] = [
                $i++,
                htmlspecialchars($item->item_name),
                htmlspecialchars($item->category),
                htmlspecialchars($item->brand          ?? '-'),
                htmlspecialchars($item->Model          ?? '-'),
                htmlspecialchars($item->serial_number  ?? '-'),
                $item->quantity           ?? 0,
                $item->available_quantity ?? 0,
                $item->borrowed_quantity  ?? 0,
                $badge,
                htmlspecialchars($item->location ?? '-'),
                date('M d, Y h:i A', strtotime($item->created_at)),
                date('M d, Y h:i A', strtotime($item->updated_at)),
                $action,
            ];
        }

        // Return JSON
        echo json_encode([
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total,
            'recordsFiltered' => (int) $filtered,
            'data'            => $data,
        ]);
    }

    public function sync_itemized()
    {
        $this->load->model('Itemized_model');
        $items = $this->Item_model->get_all();

        foreach ($items as $item) {
            $existing = $this->Itemized_model->count_by_item_id($item->id);

            if ($existing === 0 && $item->quantity > 0) {
                $units = [];
                for ($i = 1; $i <= $item->quantity; $i++) {
                    $units[] = [
                        'item_id'          => $item->id,
                        'unit_no'          => $i,
                        'status'           => 'available',
                        'item_condition'   => 'new',
                        'item_description' => $item->item_name . ' unit ' . $i,
                    ];
                }
                $this->Itemized_model->insert_batch($units);
            }
        }

        echo "Sync done! All items now have itemized units.";
    }

    public function repair_quantities()
    {
        $fixed = $this->Item_model->recalculate_all_from_itemized();
        echo json_encode(['success' => true, 'message' => $fixed . ' item(s) recalculated from itemized units.']);
    }
}
