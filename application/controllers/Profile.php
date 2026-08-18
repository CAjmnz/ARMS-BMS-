<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Profile_model');
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->Profile_model->get_by_id($user_id);

        if (!$user) {
            redirect('auth');
        }
        $this->load->model('System_user_model');//only if not already loaded elsewhere
        $employee = $this->Profile_model->get_employee_by_username($user->username);

        $data['title']      = 'My Profile - ARMS-BMS';
        $data['page_label'] = 'My Profile';
        $data['user']       = $user;
        $data['employee']   = $employee;//FALSE/null if not matching system_users row 

        $this->load->view('profile/index', $data);
    }

    // AJAX — change username
    public function update_username()
    {
        if ($this->input->method() !== 'post') {
            redirect('myprofile');
        }

        $user_id  = $this->session->userdata('user_id');
        $username = trim($this->input->post('username'));

        if (empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Username cannot be empty.']);
            return;
        }

        if (strlen($username) < 3) {
            echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters.']);
            return;
        }

        if ($this->Profile_model->username_exists($username, $user_id)) {
            echo json_encode(['success' => false, 'message' => 'That username is already taken.']);
            return;
        }

        if ($this->Profile_model->update_username($user_id, $username)) {
            // Keep the session in sync so the topbar/greeting updates immediately
            $this->session->set_userdata('username', $username);
            echo json_encode(['success' => true, 'message' => 'Username updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update username.']);
        }
    }

    // AJAX — change password
    public function update_password()
    {
        if ($this->input->method() !== 'post') {
            redirect('myprofile');
        }

        $user_id          = $this->session->userdata('user_id');
        $current_password = $this->input->post('current_password');
        $new_password      = $this->input->post('new_password');
        $confirm_password  = $this->input->post('confirm_password');

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all password fields.']);
            return;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
            return;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match.']);
            return;
        }

        $user = $this->Profile_model->get_by_id($user_id);

        if (!$user || !password_verify($current_password, $user->password)) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            return;
        }

        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        if ($this->Profile_model->update_password($user_id, $hashed)) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
        }
    }
}