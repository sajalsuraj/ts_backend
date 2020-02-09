<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Invoice extends CI_Controller{
  public function __construct()
  {
    parent::__construct();
    //Codeigniter : Write Less Do More
  }
  function index()
  {
    $this->load->library('pdfgenerator');
    $view = $this->load->view('invoice/invoice',array(),TRUE);
    $filename = 'report_'.time();
    $this->pdfgenerator->generate($view, $filename, true, 'A4', 'portrait');
    exit();
  }
}