<?php defined('BASEPATH') or exit('No direct script access allowed');

class Item_model extends CI_Model
{

    private $primary_key = 'QID';
    private $table = 'items';


    //get all items (with optional filters)
    public function get_all($filters = [])
    {
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }
    //get single item by ID
    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }
    //add new item 
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }
    //update item
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
    //delete item
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
    // barcode future 
    public function barcode_exists($barcode, $exclude_id = null)
    {
        $this->db->where('barcode', $barcode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }
    // Define searchable/sortable columns
    private $columns = [
        0  => 'id',
        1  => 'item_name',
        2  => 'category',
        3  => 'brand',
        4  => 'Model',
        5  => 'serial_number',
        6  => 'quantity',
        7  => 'available_quantity',
        8  => 'borrowed_quantity',
        9  => 'status',
        10 => 'location',
        11 => 'created_at',
        12 => 'updated_at',
    ];

    // Count total records (no filter, no search)
    public function count_total()
    {
        return $this->db->count_all($this->table);
    }

    // Count filtered records (with search + filters)
    public function count_filtered($search = '', $filters = [])
    {
        $this->_apply_filters($filters);
        $this->_apply_search($search);
        return $this->db->count_all_results($this->table);
    }

    // Get paginated rows (with search + filters + sort)
    public function get_datatables($limit, $start, $search = '', $order_col = 0, $order_dir = 'asc', $filters = [])
    {
        $this->_apply_filters($filters);
        $this->_apply_search($search);

        $col = isset($this->columns[$order_col]) ? $this->columns[$order_col] : 'id';
        $this->db->order_by($col, $order_dir);
        $this->db->limit($limit, $start);

        return $this->db->get($this->table)->result();
    }

    // Private — apply filters
    private function _apply_filters($filters = [])
    {
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }
    }

    // Private — apply search
    private function _apply_search($search = '')
    {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item_name', $search);
            $this->db->or_like('category', $search);
            $this->db->or_like('brand', $search);
            $this->db->or_like('serial_number', $search);
            $this->db->or_like('location', $search);
            $this->db->group_end();
        }
    }
}
