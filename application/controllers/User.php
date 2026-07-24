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
}
