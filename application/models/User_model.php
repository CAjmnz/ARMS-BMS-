<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function check_login($username, $password) {
        $this->db->where('username', $username);
        $query = $this->db->get('users');
        $user  = $query->row();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return FALSE;
    }

    //Get user by username only (no password check)

    public function get_by_username($username)
    {
       return $this->db->get_where('users',['username' => $username])->row();

    }

    //Increment failed attempts
    public function increment_attempts($username)
    {
      $this->db->where('username', $username);
      $this->db->set('login_attempts', 'login_attempts + 1',FALSE);
      $this->db->update('users');
    }
    //Lock account for 30 seconds
    public function lock_account($username)
    {
        $locked_until = date('Y-m-d H:i:s', strtotime('+30 seconds'));
        $this->db->where('username',$username);
        $this->db->update('user', ['locked_until' => $locked_until ]);
    }

    //Reset attempts after successful login 
    public function reset_attempts($username)
    {
        $this->db->where('username', $username);
        $this->db->update('users',[
            'login_attempts' => 0,
            'locked_until'   => NULL
        ]);
    }
}