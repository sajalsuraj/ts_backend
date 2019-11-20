<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller{

    public function __construct(){
      parent::__construct();
    }

    public function logout(){
    	
    	if($this->session->userdata('type') == "admin" || $this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "worker"){
    		$this->session->unset_userdata('type');
            $this->session->unset_userdata('name');
            $this->session->unset_userdata('id');
    		redirect('users/login');
    	}

    }
}