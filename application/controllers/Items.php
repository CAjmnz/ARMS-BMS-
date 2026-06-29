<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Items extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Item_model');
        $this->load->library('session');
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

        $data = [
            'item_name'          => trim($this->input->post('item_name')),
            'category'           => trim($this->input->post('category')),
            'brand'              => trim($this->input->post('brand')),
            'Model'              => trim($this->input->post('model')),
            'serial_number'      => trim($this->input->post('serial_number')),
            'quantity'           => (int) $this->input->post('quantity'),
            'available_quantity' => (int) $this->input->post('available_quantity'),
            'borrowed_quantity'  => (int) $this->input->post('borrowed_quantity'),
            'status'             => $this->input->post('status'),
            'location'           => trim($this->input->post('location')),
        ];

        if ($this->Item_model->insert($data)) {
            echo json_encode(['success' => true, 'message' => 'Item added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add item.']);
        }
    }
    //get individual item
    public function get($id)
    {
        $item = $this->Item_model->get_by_id($id);
        if ($item) {
            echo json_encode(['success' => true, 'item' => $item]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found.']);
        }
    }
    // update item
    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('items');
        }

        $data = [
            'item_name'          => trim($this->input->post('item_name')),
            'category'           => trim($this->input->post('category')),
            'brand'              => trim($this->input->post('brand')),
            'Model'              => trim($this->input->post('model')),
            'serial_number'      => trim($this->input->post('serial_number')),
            'quantity'           => (int) $this->input->post('quantity'),
            'available_quantity' => (int) $this->input->post('available_quantity'),
            'borrowed_quantity'  => (int) $this->input->post('borrowed_quantity'),
            'status'             => $this->input->post('status'),
            'location'           => trim($this->input->post('location')),
        ];

        if ($this->Item_model->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => 'Item updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update item.']);
        }
    }

    //Delete item 
    public function delete($id)
    {
        if ($this->Item_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'item deleted successfully.']);
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
    public function ajax_list() {
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
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item btnEdit" data-id="' . $item->id . '">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="dropdown-item btnDelete"
                        data-id="' . $item->id . '"
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
}
