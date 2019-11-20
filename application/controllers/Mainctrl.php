<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mainctrl extends CI_Controller {
    function __construct()
    {
        parent::__construct();
    }

    public function frontend($page = 'home') {

        if (!file_exists(APPPATH . 'views/pages/frontend/' . $page . '.php')) {
            // Whoops, we don't have a page for that!
            show_404();
            exit;
        }
        
        $data['page'] = $page;
        //$data['title'] = ucfirst($page); // Capitalize the first letter
        $this->load->view('templates/frontend/header', $data);
        $this->load->view('pages/frontend/' . $page, $data);
        $this->load->view('templates/frontend/footer', $data);
    }
    
    public function view($page = 'home'){
		if ( ! file_exists(APPPATH.'views/pages/admin/'.$page.'.php'))
        {
                // Whoops, we don't have a page for that!
                show_404();
        }
        $data['title'] = ucfirst($page); // Capitalize the first letter
        $this->load->view('templates/admin/header', $data);
        $this->load->view('pages/admin/'.$page, $data);
        $this->load->view('templates/admin/footer', $data);
    }
}