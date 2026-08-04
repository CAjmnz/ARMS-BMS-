<?php defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    private $allowed_roles = array('Admin', 'User');
    private $allowed_statuses = array('Active', 'Inactive');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->load->model('System_user_model');
        $this->load->library('session');
        $this->load->helper('url');


        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
            return;
        }

        // Employee photos are available to every authenticated account.
        // All other user-management methods require an administrator role.
        $method = $this->router->fetch_method();
        $role   = (string) $this->session->userdata('role');

        if ($method !== 'photo_proxy' &&
            !in_array($role, array('Super-admin', 'Admin'), TRUE)) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_status_header(403);
                $this->output->set_content_type('application/json');
                echo json_encode(array(
                    'success' => FALSE,
                    'message' => 'You are not authorized to manage users.'
                ));
                exit;
            }

            show_error('You are not authorized to manage users.', 403);
        }
    }

    public function index()
    {
        $data['title']      = 'Users - ARMS-BMS';
        $data['page_label'] = 'Users';
        $data['summary']    = $this->Dashboard_model->get_summary();
        $data['activity']   = $this->Dashboard_model->get_recent_activity();

        $this->load->view('user/index', $data);
    }
    // AJAX — chart data for category breakdown
    public function chart_categories()
    {
        $data = $this->Dashboard_model->get_category_breakdown();
        $labels = [];
        $values = [];
        foreach ($data as $row) {
            $labels[] = $row->category;
            $values[] = (int) $row->total_quantity;
        }
        echo json_encode(['labels' => $labels, 'values' => $values]);
    }

    // AJAX — chart data for borrowing trend
    public function chart_trend()
    {
        $trend = $this->Dashboard_model->get_borrowing_trend(12);
        $labels = [];
        $values = [];
        foreach ($trend as $month => $count) {
            $labels[] = date('M Y', strtotime($month . '-01'));
            $values[] = $count;
        }
        echo json_encode(['labels' => $labels, 'values' => $values]);
    }

    // Search employees via the external RMS API — pass the API response straight through
    public function search_employee()
    {
        $query = $this->input->get('q');

        if (empty($query)) {
            echo json_encode(['data' => ['employee' => []]]);
            return;
        }

        $api_url = 'http://172.16.161.34/api/rms/monitoring/search/name?q=' . urlencode($query);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error || $http_code !== 200) {
            echo json_encode(['data' => ['employee' => []]]);
            return;
        }

        // Pass the API's response straight through — JS already expects this exact shape
        echo $response;
    }
    public function save_user()
    {
        if ($this->input->method() !== 'post') {
            redirect('user');
            return;
        }

        $employee_id   = trim((string) $this->input->post('employee_id', TRUE));
        $employee_name = trim((string) $this->input->post('employee_name', TRUE));
        $role          = (string) $this->input->post('role', TRUE);
        $account_status = (string) $this->input->post('account_status', TRUE);

        if ($employee_id === '' || $employee_name === '') {
            echo json_encode(['success' => false, 'message' => 'Employee ID and employee name are required.']);
            return;
        }

        if (!in_array($role, $this->allowed_roles, TRUE) ||
            !in_array($account_status, $this->allowed_statuses, TRUE)) {
            echo json_encode(['success' => false, 'message' => 'Invalid role or account status.']);
            return;
        }

        if ($this->System_user_model->exists($employee_id)) {
            echo json_encode(['success' => false, 'message' => 'This employee is already added as a system user.']);
            return;
        }

        $default_password = 'bms-2026';

        $data = [
            'employee_id'       => $employee_id,
            'employee_name'     => $employee_name,
            'employee_position' => $this->input->post('employee_position', TRUE),
            'employee_type'     => $this->input->post('employee_type', TRUE),
            'employee_status'   => $this->input->post('employee_status', TRUE),
            'employee_company'  => $this->input->post('employee_company', TRUE),
            'employee_bunit'    => $this->input->post('employee_bunit', TRUE),
            'employee_dept'     => $this->input->post('employee_dept', TRUE),
            'employee_photo'    => $this->input->post('employee_photo', TRUE),
            'password'          => password_hash($default_password, PASSWORD_DEFAULT),
            'password_change_count' => 0,
            'role'              => $role,
            'account_status'    => $account_status,
        ];

        if ($this->System_user_model->insert($data)) {
            echo json_encode(['success' => true, 'message' => 'User added successfully. Default password: bms-2026']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add user.']);
        }
    }
    // AJAX list for server-side DataTables
    public function ajax_list()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';
        $order     = $this->input->post('order');
        $order_col = $order[0]['column'] ?? 0;
        $order_dir = $order[0]['dir']    ?? 'asc';

        $filters = [
            'role'           => $this->input->post('role'),
            'account_status' => $this->input->post('account_status'),
        ];

        $total    = $this->System_user_model->count_total();
        $filtered = $this->System_user_model->count_filtered($search, $filters);
        $users    = $this->System_user_model->get_datatables($length, $start, $search, $order_col, $order_dir, $filters);

        $data = [];
        $i    = (int) $start + 1;

        foreach ($users as $user) {

            $role_badge = $user->role === 'Admin'
                ? '<span class="badge badge-danger">Admin</span>'
                : '<span class="badge badge-secondary">User</span>';

            $status_badge = $user->account_status === 'Active'
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-dark">Inactive</span>';

 // Build initials from the name
$name_parts = preg_split('/[\s,]+/', trim($user->employee_name ?? ''));
$initials = '';
foreach (array_slice($name_parts, 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
$initials = $initials ?: '?';

$photo_url = !empty($user->employee_photo)
    ? base_url('user/photo_proxy?path=' . urlencode($user->employee_photo))
    : null;

if ($photo_url) {
    $avatar_html = '<img src="' . $photo_url . '" alt="Photo"
        style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:1px solid #e3e6f0; flex-shrink:0;"
        onerror="this.outerHTML=\'<div style=&quot;width:36px;height:36px;border-radius:50%;background:#2563B8;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;flex-shrink:0;&quot;>' . $initials . '</div>\'">';
} else {
    $avatar_html = '<div style="width:36px; height:36px; border-radius:50%; background:#2563B8; color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; flex-shrink:0;">' . $initials . '</div>';
}

$employee_cell = '
    <div style="display:flex; align-items:center; gap:10px;">
        ' . $avatar_html . '
        <span>' . htmlspecialchars($user->employee_name) . '</span>
    </div>';
            $action = '
        <div class="dropdown">
            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <div class="dropdown-menu">
                <button class="dropdown-item btnEditUser" data-id="' . encode_id($user->id) . '">
                    <i class="fas fa-edit"></i> Edit
                </button>
                  <button class="dropdown-item btnResetPassword" data-id="' . encode_id($user->id) . '" data-name="' . htmlspecialchars($user->employee_name) . '">
        <i class="fas fa-key"></i> Reset Password
    </button>
                <button class="dropdown-item btnDeleteUser"
                    data-id="' . encode_id($user->id) . '"
                    data-name="' . htmlspecialchars($user->employee_name) . '">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>';

            $data[] = [
                $i++,
                $employee_cell,
                htmlspecialchars($user->employee_id),
                htmlspecialchars($user->employee_position ?? '-'),
                htmlspecialchars($user->employee_dept ?? '-'),
                htmlspecialchars($user->employee_status ?? '-'),
                htmlspecialchars($user->employee_bunit ?? '-'),
                $role_badge,
                $status_badge,
                $action,
            ];
        }

        echo json_encode([
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total,
            'recordsFiltered' => (int) $filtered,
            'data'            => $data,
        ]);
    }

    // Get a single system user (for Edit modal)
    public function get($id)
    {
        $decoded_id = decode_id($id);
        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $user = $this->System_user_model->get_by_id($decoded_id);
        if ($user) {
            echo json_encode(['success' => true, 'item' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
    }

    // Update role/account_status only (employee data stays read-only, sourced from API)
    public function update_user($id)
    {
        $decoded_id = decode_id($id);
        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        if ($this->input->method() !== 'post') {
            redirect('user');
            return;
        }

        $role           = (string) $this->input->post('role', TRUE);
        $account_status = (string) $this->input->post('account_status', TRUE);

        if (!in_array($role, $this->allowed_roles, TRUE) ||
            !in_array($account_status, $this->allowed_statuses, TRUE)) {
            echo json_encode(['success' => false, 'message' => 'Invalid role or account status.']);
            return;
        }

        $data = [
            'role'           => $role,
            'account_status' => $account_status,
        ];

        if ($this->System_user_model->update($decoded_id, $data)) {
            echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user.']);
        }
    }

    // Delete a system user
    public function delete($id)
    {
        $decoded_id = decode_id($id);
        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $user = $this->System_user_model->get_by_id($decoded_id);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            return;
        }

        if ($this->System_user_model->delete($decoded_id)) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
    }
    // Reset password
    public function reset_password($id)
    {
        $decoded_id = decode_id($id);
        if ($decoded_id === null) {
            echo json_encode(['success' => false, 'message' => 'invalid request.']);
            return;
        }

        $default_password = 'bms-2026';

        if ($this->System_user_model->set_password($decoded_id, $default_password)) {
            echo json_encode(['success' => true, 'message' => 'Password reset to default (bms-2026).']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
        }
    }
    
   // Proxy employee photos from the external API through our own domain
public function photo_proxy()
{
    $relative_path = $this->input->get('path');

    if (!is_string($relative_path) || $relative_path === '') {
        show_404();
        return;
    }

    // Same cleanup logic as the JS resolvePhotoUrl() — strip leading ../
    $clean_path = preg_replace('#^(\.\./)+#', '', str_replace('\\', '/', $relative_path));
    $clean_path = ltrim($clean_path, '/');

    if ($clean_path === '' || strpos($clean_path, '..') !== FALSE ||
        preg_match('#^[a-z][a-z0-9+.-]*://#i', $clean_path)) {
        show_404();
        return;
    }
    $api_url =  'http://172.16.161.34:8080/hrms/' . $clean_path;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $image_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error || $http_code !== 200 || empty($image_data) ||
        strpos((string) $content_type, 'image/') !== 0) {
        // Return a 1x1 transparent placeholder instead of a broken image icon
        header('Content-Type: image/png');
        echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        return;
    }

    header('Content-Type: ' . ($content_type ?: 'image/jpeg'));
    header('Cache-Control: public, max-age=3600'); // cache for 1 hour to avoid re-fetching every page load
    echo $image_data;
}
}
