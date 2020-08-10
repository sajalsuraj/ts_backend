<?php

    defined('BASEPATH') OR exit('No direct script access allowed');

    class Customer extends CI_Model{ 

        function __construct(){
            parent::__construct();
        }

        public function login($data){
            $this->db->select('id, name, phone, otp_verified');
            $query = $this->db->get_where('customer', array('phone' => $data['phone'], 'password' => $data['password']))->row();
            return $query;
        }

        public function getProfileData($data){
            $this->db->select('id, name, email, phone, photo');
            $query = $this->db->get_where('customer', array('id' => $data))->row();
            return $query;
        }
    }

?>