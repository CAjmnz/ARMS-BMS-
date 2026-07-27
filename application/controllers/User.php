<?php defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->load->model('System_user_model');
        $this->load->library('session');
        $this->load->helper('url');


        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
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
    }

    $employee_id = $this->input->post('employee_id');

    if (empty($employee_id)) {
        echo json_encode(['success' => false, 'message' => 'Missing employee ID.']);
        return;
    }

    if ($this->System_user_model->exists($employee_id)) {
        echo json_encode(['success' => false, 'message' => 'This employee is already added as a system user.']);
        return;
    }

    $data = [
        'employee_id'       => $employee_id,
        'employee_name'     => $this->input->post('employee_name'),
        'employee_position' => $this->input->post('employee_position'),
        'employee_type'     => $this->input->post('employee_type'),
        'employee_status'   => $this->input->post('employee_status'),
        'employee_company'  => $this->input->post('employee_company'),
        'employee_bunit'    => $this->input->post('employee_bunit'),
        'employee_dept'     => $this->input->post('employee_dept'),
        'employee_photo'    => $this->input->post('employee_photo'),
        'role'              => $this->input->post('role'),
        'account_status'    => $this->input->post('account_status'),
    ];

    if ($this->System_user_model->insert($data)) {
        echo json_encode(['success' => true, 'message' => 'User added successfully.']);
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
                <button class="dropdown-item btnDeleteUser"
                    data-id="' . encode_id($user->id) . '"
                    data-name="' . htmlspecialchars($user->employee_name) . '">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>';

        $data[] = [
            $i++,
            htmlspecialchars($user->employee_name),
            htmlspecialchars($user->employee_id),
            htmlspecialchars($user->employee_dept ?? '-'),
            htmlspecialchars($user->employee_status ?? '-'),
            htmlspecialchars($user->employee_bunit ?? '-'),
            htmlspecialchars($user->employee_type ?? '-'),
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
    }

    $data = [
        'role'           => $this->input->post('role'),
        'account_status' => $this->input->post('account_status'),
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
}
