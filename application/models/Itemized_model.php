<?php defined('BASEPATH') or exit('No direct script access allowed');

class Itemized_model extends CI_Model
{

    private $table = 'itemized';


  //Searchable/sortable columns for DataTables
    private $columns = [
        0  => 'id',
        1  => 'item_id',
        2  => 'unit_no',
        3  => 'status',
        4  => 'item_condition',
        5  => 'item_description',
        6  => 'created_at',
        7 => 'updated_at',
    ];

    // Get all itemized unit (joined with parent item info)
    public function get_all($filters =[])
    {
       $this->db->select('itemized.*,items.item_name');
       $this->db->from($this->table);
       $this->db->join('items','items.id = itemized.item_id');

       $this->_apply_filters($filters);
       $this->db->order_by('itemized.created_at','DESC');
       return $this->db->get()->result();
    }
    // Get single unit ID 
    public function get_by_id($id)
    {
        $this->db->select('itemized,*,items.item_name');
        $this->db->from($this->table);
        $this->db->join('items','items.id = itemized.item_id');
        $this->db->where('itemized.id', $id);
        return $this->db->get()->row();
    }
    //Get all units belonging to one item
    public function get_by_item_id($item_id)
    {
        return $this->db->get_where($this->table,['item_id' => $item_id])->result();
    }

    // Add new unit
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }
    // Add multiple units at once (used when item quantity is set)
    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }
    // Update unit 
    public function update($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update($this->table,$data);
    }
    // Delete unit
    public function delete($id)
    {
        return $this->db->delete($this->table,['id' => $id]);
    }
    // Delete all units belonging to an item (used when item is deleted)
    public function delete_by_item_id($item_id)
    {
        return $this->db->delete($this->table, ['item.id' => $item_id]);
    }
    // Count total records 
    public function count_total()
    {
        return $this->db->count_all($this->table);
    }
    // Count filtered records
    public function count_filtered($search = '', $filters = [])
    {
        $this->db->from($this->table);
        $this->db->join('items','items.id = itemized.item_id');
        $this->_apply_filters($filters);
        $this->_apply_search($search);
        return $this->db->count_all_results();

    }
    // Get paginated rows (with search + filters + sort)
    public function get_datatables($limit, $start, $search = '', $order_col = 0, $order_dir = 'asc', $filters = [])
{
    $this->db->select('itemized.*, items.item_name');
    $this->db->from($this->table);
    $this->db->join('items', 'items.id = itemized.item_id');

    $this->_apply_filters($filters);
    $this->_apply_search($search);

    $col = isset($this->columns[$order_col]) ? $this->columns[$order_col] : 'id';
    $this->db->order_by('itemized.' . $col, $order_dir);
    $this->db->limit($limit, $start);

    return $this->db->get()->result();
}
    // Private — apply filters
    private function _apply_filters($filters = [])
    {
        if (!empty($filters['status'])) {
            $this->db->where('itemized.status', $filters['status']);
        }
        if (!empty($filter['item_condition'])) {
            $this->db->where('itemized.item_condition',$filters['item_condition']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(itemized.created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(itemized.created_at) <=', $filters['date_to']);
        }
    }

    // Private — apply search
    private function _apply_search($search = '')
    {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('items.item_name', $search);
            $this->db->or_like('itemized.item_description', $search);
            $this->db->or_like('itemized.status', $search);
            $this->db->group_end();
        }
    }
}
