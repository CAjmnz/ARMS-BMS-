<?php defined('BASEPATH') or exit('No direct script access allowed');

class Reservation_model extends CI_Model
{
    private $table = 'reservation_items';

    // Searchable/sortable columns for DataTables
    private $columns = [
        0  => 'borrowers.id_number',
        1  => 'borrowers.full_name',
        2  => 'items.item_name',
        3  => 'items.category',
        4  => 'reservations.reservation_date',
        5  => 'reservations.due_status',
        6  => 'reservations.status',
    ];

    private function _base_query()
    {
    $this->db->select('
        reservation_items.id,
        reservation_items.unit_id,
        reservation_items.status as item_status,
        reservations_id as reservation_id,
        reservations.date_requested,
        reservations.due_date,
        reservations.status as reservation_status,
        reservations.purpose,
        borrowers.id_number,
        borrowers.full_name as borrower_name,
        items.item_name,
        items.category,
        itemized.unit_no,
        users.username as released_by_name
    ');
    $this->db->from($this->table);
    $this->db->join('reservations', 'reservations.id = reservation_items.reservation_id', 'left');
    $this->db->join('borrowers', 'borrowers.id = reservations.borrower_id', 'left');
    $this->db->join('itemized', 'itemized.id = reservation_items.unit_id', 'left');
    $this->db->join('items', 'items.id = itemized.item_id', 'left');
    $this->db->join('users', 'users.id = reservations.requested_by', 'left');
    $this->db->where('reservation_items.status', 'reserved');
    }

    public function get_item_by_item($id)
    {
        $this->_base_query();
        $this->db->where('reservation_items.id',$id);
        return $this->db->get()->row();
    }

    public function count_total()
    {
        $this->base_query();
        return $this->db->count_all_results();
    }

    public function count_filtered($search = '',$filters = [])
    {
        $this->_base_query();
        $this->_apply_filters($filters);
        $this->_apply_search($search);
        return $this->db->count_all_results();
    }

    public function get_datatables($limit,$start, $search = '', $order_col = 0, $order_dir = 'asc', $filters = [])
    {
        $this->_base_query();
        $this->_apply_filters($filters);
        $this->_apply_search($search);
 
        $col = isset($this->columns[$order_col])? $this->column[$order_col]: 'reservation.reservation_date';
        $this->db->order_by($col, $order_dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }
    private function _apply_filters($filters = [])
    {
        if (!empty($filters['reservation_status'])) {
            $this->db->where('reservations.status', $filters['reservation_status']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(reservations.reservation_date) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(reservations.reservation_date) <=', $filters['date_to']);
        }
    }

    private function _apply_search($search = '')
    {
        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('borrowers.id_number',$search);
            $this->db->or_like('borrowers.full_name',$search);
            $this->db->or_like('items.item_name',$search);
            $this->db->or_like('items.category',$search);
            $this->db->group_end();
        }
    }
    
    private function get_available_unit($item_id)
    {
        $this->db->select('id,unit_no, item_condition');
        $this->db->where('item_id, $item_id');
        $this->db->where('status','available');
        $this->db->order_by('unit_no','asc');
        return $this->db->get('itemized')->result();
    }

    public function create_reservation($header, $unit_ids)
    {
        $this->db->trans_start();

        $this->db->insert('reservations', $header);
        $reservation_id = $this->db->insert_id();

        foreach ($unit_ids as $unit_id) {
            $this->db->insert('reservation_items', [
                'reservation_id' => $reservation_id,
                'unit_id'        => $unit_id,
                'status'         => 'reserved',
            ]);

            $this->db->where('id', $unit_id);
            $this->db->update('itemized', ['status' => 'reserved']);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $reservation_id : false;
    }

    public function approve($reservation_id, $approved_by)
    {
        $this->db->where('id', $reservation_id);
        return $this->db->update('reservations',[
            'status'        => 'approved',
            'approved_by'   => $approved_by,
            'date_approved' => date('Y-m-d H:i:s'),
        ]);

    }
    public function reject($reservation_id)
    {
        $this->db->where('id',$reservation_id);
        $this->db->update('reservations',['status' => 'rejected']);

        $this->db->where('reservation_id', $reservation_id);
        $this->db->update('reservation_items',['status' => 'cancelled']);

    }
    public function get_items_for_reservation($reservation_id)
    {
        return $this
    }
    public function mark_items_released($reservation_id)
    {

    }



}
