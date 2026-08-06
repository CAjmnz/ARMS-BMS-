<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile_model extends CI_Model
{
    private $table = 'users';

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function username_exists($username, $exclude_id = null)
    {
        $this->db->where('username', $username);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function update_username($id, $username)
    {
        return $this->db->where('id', $id)->update($this->table, ['username' => $username]);
    }

    public function update_password($id, $hashed_password)
    {
        return $this->db->where('id', $id)->update($this->table, ['password' => $hashed_password]);
    }
}