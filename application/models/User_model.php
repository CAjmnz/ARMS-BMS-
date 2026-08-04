<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    public function get_by_username($username)
    {
        return $this->db
            ->where('username', $username)
            ->get($this->table)
            ->row();
    }

    public function check_login($username, $password)
    {
        $user = $this->get_by_username($username);

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return FALSE;
    }

    public function increment_attempts($username)
    {
        return $this->db
            ->where('username', $username)
            ->set('login_attempts', 'login_attempts + 1', FALSE)
            ->update($this->table);
    }

    public function lock_account($username, $locked_until = NULL)
    {
        if ($locked_until === NULL) {
            $locked_until = time() + 30;
        }

        return $this->db
            ->where('username', $username)
            ->update($this->table, array(
                'locked_until' => date('Y-m-d H:i:s', (int) $locked_until)
            ));
    }

    public function reset_attempts($username)
    {
        return $this->db
            ->where('username', $username)
            ->update($this->table, array(
                'login_attempts' => 0,
                'locked_until'   => NULL
            ));
    }

    public function reset_password($id, $plain_password = 'bms-2026')
    {
        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, array(
                'password'       => password_hash($plain_password, PASSWORD_DEFAULT),
                'login_attempts' => 0,
                'locked_until'   => NULL
            ));
    }
}
