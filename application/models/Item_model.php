<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item_model extends CI_Model{

    private $primary_key = 'QID';
    private $table = 'items';


    //get all items (with optional filters)
    public function get_all($filters = []){
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        if (!empty($filters['status'])){
            $this->db->where('status',$filters['status']);
        } 
        if(!empty($filters['date_from'])){
            $this->db->where('DATE(created_at) >=', $filters['date_from']); 
        }
        if(!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }
        $this->db->order_by('created_at','DESC');
        return $this->db->get($this->table)->result();
    }
    //get single item by ID
    public function get_by_id($id){
        return $this->db->get_where($this->table,['id'=> $id])->row();
    }
    //add new item 
    public function insert($data){
        return $this->db->insert($this->table,$data);
    }
    public function update($id,$data){
        $this->db->where('id',$id);
        return $this->db->update($this->table, $data);
    }
    public function delete($id){
        return $this->db->delete($this->table,['id'=>$id]);
    }
    public function barcode_exists($barcode,$exclude_id = null){
        $this->db->where('barcode', $barcode);
        if($exclude_id){
        $this->db->where('id !=', $exclude_id);
    }
    return $this->db->count_all_results($this->table) > 0;
}
}