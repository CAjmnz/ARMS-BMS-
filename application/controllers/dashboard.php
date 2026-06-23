<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        
  

     // CI3 way of checking session (not $_SESSION)
       if(!$this->session->userdata('logged_in')){
		   redirect('auth');
	   }  
	}
    public function index()
    {
        $data['username'] = $this->session->userdata('username');
		$this->load->view('dashboard/index',$data);
    }


    // Logout
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}


