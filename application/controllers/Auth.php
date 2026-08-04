<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    private $max_attempts = 5;
    private $lock_seconds = 30;

    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('Asia/Manila');

        $this->load->model('User_model');
        $this->load->model('System_user_model');
        $this->load->library(array('session', 'form_validation'));
        $this->load->helper(array('form', 'url'));
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }

        $data['title'] = 'Login - ARMS-BMS';
        $this->load->view('auth/login', $data);
    }

    public function login()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth');
            return;
        }

        $credential = trim((string) $this->input->post('username', TRUE));
        $password   = (string) $this->input->post('password');

        if ($credential === '' || $password === '') {
            $this->_login_error('Username/Employee ID and password are required.');
            return;
        }

        // Existing Super-admin/admin accounts continue to use the users table.
        $admin = $this->User_model->get_by_username($credential);

        if ($admin) {
            $this->_login_admin($admin, $password);
            return;
        }

        // Added users authenticate with employee_id from system_users.
        $system_user = $this->System_user_model->get_by_employee_id($credential);

        if (!$system_user) {
            $this->_login_error('Invalid username/Employee ID or password.');
            return;
        }

        $this->_login_system_user($system_user, $password);
    }

    private function _login_admin($user, $password)
    {
        if (!empty($user->locked_until)) {
            $locked_until = strtotime($user->locked_until);

            if ($locked_until !== FALSE && time() < $locked_until) {
                $this->_locked_error($locked_until);
                return;
            }

            $this->User_model->reset_attempts($user->username);
            $user->login_attempts = 0;
            $user->locked_until   = NULL;
        }

        if (password_verify($password, $user->password)) {
            $this->User_model->reset_attempts($user->username);

            $role = strcasecmp($user->username, 'Super-admin') === 0
                ? 'Super-admin'
                : 'Admin';

            $this->_start_session(array(
                'user_id'         => (int) $user->id,
                'system_user_id'  => NULL,
                'account_source'  => 'users',
                'username'        => $user->username,
                'employee_id'     => '',
                'firstname'       => '',
                'lastname'        => '',
                'full_name'       => $user->username,
                'email'           => '',
                'role'            => $role,
                'profile_picture' => ''
            ));
            return;
        }

        $username = $user->username;
        $this->User_model->increment_attempts($username);
        $user = $this->User_model->get_by_username($username);
        $attempts = $user ? (int) $user->login_attempts : $this->max_attempts;

        if ($attempts >= $this->max_attempts) {
            $locked_until = time() + $this->lock_seconds;
            $this->User_model->lock_account($username, $locked_until);
            $this->_locked_error(
                $locked_until,
                'Too many failed attempts. Account locked for 30 seconds.'
            );
            return;
        }

        $attempts_left = $this->max_attempts - $attempts;
        $this->_login_error(
            'Invalid username/Employee ID or password. ' .
            $attempts_left . ' attempt(s) remaining.'
        );
    }

    private function _login_system_user($user, $password)
    {
        if (strcasecmp((string) $user->account_status, 'Active') !== 0) {
            $this->_login_error('This account is inactive. Please contact an administrator.');
            return;
        }

        $lock_key = hash('sha256', strtolower((string) $user->employee_id));
        $locks    = $this->_get_system_locks();
        $lock     = isset($locks[$lock_key]) ? $locks[$lock_key] : NULL;

        if ($lock && !empty($lock['locked_until'])) {
            $locked_until = (int) $lock['locked_until'];

            if (time() < $locked_until) {
                $this->_locked_error($locked_until);
                return;
            }

            unset($locks[$lock_key]);
            $this->_save_system_locks($locks);
            $lock = NULL;
        }

        if (password_verify($password, $user->password)) {
            unset($locks[$lock_key]);
            $this->_save_system_locks($locks);

            $role = in_array($user->role, array('Admin', 'User'), TRUE)
                ? $user->role
                : 'User';

            $this->_start_session(array(
                'user_id'         => (int) $user->id,
                'system_user_id'  => (int) $user->id,
                'account_source'  => 'system_users',
                'username'        => $user->employee_id,
                'employee_id'     => $user->employee_id,
                'firstname'       => $user->employee_name,
                'lastname'        => '',
                'full_name'       => $user->employee_name,
                'email'           => '',
                'role'            => $role,
                'profile_picture' => (string) $user->employee_photo
            ));
            return;
        }

        $attempts = $lock && isset($lock['attempts'])
            ? (int) $lock['attempts'] + 1
            : 1;

        if ($attempts >= $this->max_attempts) {
            $locked_until = time() + $this->lock_seconds;
            $locks[$lock_key] = array(
                'attempts'     => $attempts,
                'locked_until' => $locked_until
            );
            $this->_save_system_locks($locks);
            $this->_locked_error(
                $locked_until,
                'Too many failed attempts. Account locked for 30 seconds.'
            );
            return;
        }

        $locks[$lock_key] = array(
            'attempts'     => $attempts,
            'locked_until' => 0
        );
        $this->_save_system_locks($locks);

        $attempts_left = $this->max_attempts - $attempts;
        $this->_login_error(
            'Invalid username/Employee ID or password. ' .
            $attempts_left . ' attempt(s) remaining.'
        );
    }

    private function _start_session($session_data)
    {
        $this->session->sess_regenerate(TRUE);
        $session_data['logged_in'] = TRUE;
        $this->session->set_userdata($session_data);
        redirect('dashboard');
    }

    private function _get_system_locks()
    {
        $locks = $this->session->userdata('system_login_locks');
        return is_array($locks) ? $locks : array();
    }

    private function _save_system_locks($locks)
    {
        $this->session->set_userdata('system_login_locks', $locks);
    }

    private function _locked_error($locked_until, $message = '')
    {
        if ($message === '') {
            $seconds_left = max(1, (int) $locked_until - time());
            $message = 'Account temporarily locked. Try again in ' .
                $seconds_left . ' second(s).';
        }

        $this->session->set_flashdata('error', $message);
        $this->session->set_flashdata(
            'locked_until',
            date(DATE_ATOM, (int) $locked_until)
        );
        redirect('auth');
    }

    private function _login_error($message)
    {
        $this->session->set_flashdata('error', $message);
        redirect('auth');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
