<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');  // ← must be here
        $this->load->helper('url');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    // Shows the login form
    public function index()
    {
        $this->data['title'] = 'Login — ARMS-BMS';
        $this->load->view('auth/login');
    }

    // Handles form submission
    public function login()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth');
        }

        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));

        // Check if user exists
        $user = $this->User_model->get_by_username($username);

        if (!$user) {
            $this->session->set_flashdata('error', 'Invalid username or password.');
            redirect('auth');
            return;
        }

        // Check if account is locked
        if (!empty($user->locked_until)) {
            $locked_until = strtotime($user->locked_until);
            $now          = time();

            if ($now < $locked_until) {
                $seconds_left = $locked_until - $now;
                $this->session->set_flashdata(
                    'error',
                    'Account temporarily locked. Try again in ' . $seconds_left . ' second(s).'
                );
                $this->session->set_flashdata('locked_until', $user->locked_until);
                redirect('auth');
                return;
            } else {
                // Lock expired — reset attempts
                $this->User_model->reset_attempts($username);
                $user->login_attempts = 0;
                $user->locked_until   = NULL;
            }
        }

        // Check password
        if (password_verify($password, $user->password)) {
            // Success — reset attempts and set session
            $this->User_model->reset_attempts($username);

            $this->session->set_userdata([
                'user_id'         => $user->id,
                'username'        => $user->username,
                'firstname'       => $user->firstname,
                'lastname'        => $user->lastname,
                'email'           => $user->email,
                'role'            => $user->role,
                'profile_picture' => $user->profile_picture,
                'logged_in'       => TRUE
            ]);
            redirect('dashboard');
        } else {
            // Wrong password — increment attempts
            $this->User_model->increment_attempts($username);

            // Re-fetch updated attempts
            $user = $this->User_model->get_by_username($username);
            $attempts_left = 5 - $user->login_attempts;

            if ($user->login_attempts >= 5) {
                // Lock the account
                $this->User_model->lock_account($username);
                $this->session->set_flashdata(
                    'error',
                    'Too many failed attempts. Account locked for 30 seconds.'
                );
                $this->session->set_flashdata(
                    'locked_until',
                    date('Y-m-d H:i:s', strtotime('+30 seconds'))
                );
            } else {
                $this->session->set_flashdata(
                    'error',
                    'Invalid username or password. ' . $attempts_left . ' attempt(s) remaining.'
                );
            }
            redirect('auth');
        }
    }

    // Logout
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
