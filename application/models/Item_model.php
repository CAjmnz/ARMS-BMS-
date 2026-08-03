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
    // Update item — automatically keeps status in sync with quantity counts
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('items', $data);

        // Only recalculate status if this update touched quantity fields
        if ($result && (isset($data['available_quantity']) || isset($data['quantity']) || isset($data['borrowed_quantity']))) {
            $this->sync_status($id);
        }

        return $result;
    }

    // Recalculate and persist status based on current available_quantity
    public function sync_status($item_id)
    {
        $item = $this->get_by_id($item_id);
        if (!$item) {
            return false;
        }

        if ($item->quantity <= 0) {
            $status = 'unavailable';
        } elseif ($item->available_quantity <= 0) {
            $status = 'unavailable';
        } elseif ($item->available_quantity >= $item->quantity) {
            $status = 'available';
        } else {
            $status = 'in-use';
        }

        // Avoid infinite loop / unnecessary write if status already correct
        if ($item->status !== $status) {
            $this->db->where('id', $item_id);
            $this->db->update('items', ['status' => $status]);
        }
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
    // One-time repair — recalculate status for ALL items based on current quantities
    public function repair_all_statuses()
    {
        $items = $this->db->get($this->table)->result();
        $fixed = 0;

        foreach ($items as $item) {
            if ($item->quantity <= 0) {
                $correct_status = 'unavailable';
            } elseif ($item->available_quantity >= $item->quantity) {
                $correct_status = 'available';
            } else {
                $correct_status = 'in-use';
            }

            if ($item->status !== $correct_status) {

                $this->db->where('id', $item->id);
                $this->db->update($this->table, ['status' => $correct_status]);
                $fixed++;
            }
        }
        return $fixed;
    }
    // Recalculate available_quantity/borrowed_quantity/quantity from actual itemized unit counts
    public function recalculate_from_itemized($item_id)
    {
        $total     = $this->db->where('item_id', $item_id)->count_all_results('itemized');
        $available = $this->db->where('item_id', $item_id)->where('status', 'available')->count_all_results('itemized');
        $borrowed  = $this->db->where('item_id', $item_id)->where('status', 'borrowed')->count_all_results('itemized');

        $this->db->where('id', $item_id);
        $this->db->update('items', [
            'quantity'           => $total,
            'available_quantity' => $available,
            'borrowed_quantity'  => $borrowed,
        ]);

        // This will also trigger sync_status() via update() — but we bypassed update() here
        // to avoid double-counting logic, so call it explicitly:
        $this->sync_status($item_id);

        return true;
    }

    // One-time repair — recalculate counts for ALL items based on actual itemized units
    public function recalculate_all_from_itemized()
    {
        $items = $this->db->get($this->table)->result();
        foreach ($items as $item) {
            $this->recalculate_from_itemized($item->id);
        }
        return count($items);
    }
    // Total available units across all items (for the Available Items card)
public function get_total_available()
{
    $this->db->select_sum('available_quantity');
    $row = $this->db->get($this->table)->row();
    return (int) ($row->available_quantity ?? 0);
}

// Available quantity grouped by category
public function get_category_availability()
{
    $this->db->select('category, SUM(available_quantity) as total_available');
    $this->db->group_by('category');
    return $this->db->get($this->table)->result();
}

// Items grouped by status (available / in-use / unavailable)
public function get_status_breakdown()
{
    $this->db->select('status, COUNT(*) as total');
    $this->db->group_by('status');
    return $this->db->get($this->table)->result();
}
}
