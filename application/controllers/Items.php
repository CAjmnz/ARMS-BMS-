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
    public function get($id) {
        $item = $this->Item_model->get_by_id($id);
        if ($item) {
            echo json_encode(['success' => true, 'item' => $item]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found.']);
        }
    }
    public function update($id) {
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
}
