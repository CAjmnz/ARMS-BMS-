<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    // Shows the login form
    public function index()
    {
        $this->load->view('auth/login');
    }

    // Handles form submission
    public function login()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth');
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $user = $this->User_model->check_login($username, $password);

        if ($user) {
            $this->session->set_userdata([
                'user_id'   => $user->id,
                'username'  => $user->username,
                'logged_in' => TRUE
            ]);
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Invalid username or password.');
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
