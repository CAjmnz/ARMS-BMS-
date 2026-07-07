<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_attempt_model extends CI_Model
{
    private $table         = 'login_attempts';
    private $max_attempts  = 5;
    private $window        = 30; // minutes — attempts older than this don't count
    private $lockout       = 15; // minutes — how long the lockout lasts

    // Count failed attempts for this IP within the window
    public function count_recent($ip)
    {
        $window_start = date('Y-m-d H:i:s', strtotime("-{$this->window} minutes"));

        return $this->db
            ->where('ip_address', $ip)
            ->where('attempted_at >=', $window_start)
            ->count_all_results($this->table);
    }

    // Check if IP is currently locked out
    public function is_locked_out($ip)
    {
        $lockout_start = date('Y-m-d H:i:s', strtotime("-{$this->lockout} minutes"));

        $recent = $this->db
            ->where('ip_address', $ip)
            ->where('attempted_at >=', $lockout_start)
            ->count_all_results($this->table);

        return $recent >= $this->max_attempts;
    }

    // Get remaining lockout time in minutes for display
    public function get_lockout_remaining($ip)
    {
        $lockout_start = date('Y-m-d H:i:s', strtotime("-{$this->lockout} minutes"));

        $row = $this->db
            ->select('attempted_at')
            ->where('ip_address', $ip)
            ->where('attempted_at >=', $lockout_start)
            ->order_by('attempted_at', 'ASC')
            ->limit(1)
            ->get($this->table)
            ->row();

        if (!$row) return 0;

        $unlock_time  = strtotime($row->attempted_at) + ($this->lockout * 60);
        $remaining    = ceil(($unlock_time - time()) / 60);

        return max($remaining, 1);
    }

    // Log a failed attempt
    public function log_attempt($ip)
    {
        $this->db->insert($this->table, [
            'ip_address'   => $ip,
            'attempted_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Clear all attempts for this IP (on successful login)
    public function clear($ip)
    {
        $this->db->where('ip_address', $ip)->delete($this->table);
    }
}