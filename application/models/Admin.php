<?php

    defined('BASEPATH') OR exit('No direct script access allowed');

    class Admin extends CI_Model{ 

        function __construct(){
            parent::__construct();
        }

        public function login($data){

            $this->db->select('id, name, type');
            $query = $this->db->get_where('worker', array('email' => $data['email'], 'password' => $data['password']))->row();
            return $query;
        }


        public function addData($data, $type){
            return $this->db->insert($type, $data) ? true : false ;
        }

        public function getAllWorkers(){
            $query = $this->db->get_where('worker', array('type' => 'worker'));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllServices(){
            $query = $this->db->get('services');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllBanners(){
            $query = $this->db->get('banner');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getActivatedBanners(){
            $this->db->select('*');
            $query = $this->db->get_where("banner", array("status"=>'true'));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getServicesExceptLevel3(){
            $this->db->select('*');
            $query = $this->db->get_where("services", array("level!="=>'3'));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getServiceById($id){
            $this->db->select('*');
            $query = $this->db->get_where("services", array('id' => $id));
            return $query->num_rows() > 0 ? $query->row(): "None";
        }

        public function checkIfServiceHasCharge($id){
            $this->db->select('*');
            $query = $this->db->get_where("services", array('id' => $id, "rate_per_min!="=>""));
            return $query->num_rows() > 0 ? true : false;
        }

        public function checkIfServiceIsParent($id){
            $this->db->select('*');
            $query = $this->db->get_where("services", array("parent_category"=> $id));
            return $query->num_rows() > 0 ? $query->result(): false;
        }

        public function getAllParentServices(){
            $query = $this->db->get_where('services', array('parent_category' => ''));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllKYC(){
            $this->db->select('k.*, w.name as username');
            $this->db->from('worker as w, kyc as k');
            $this->db->where('w.id = k.user_id');
            $query = $this->db->get();
            return $query->result();
        }

        public function getKycByID($id){
            $this->db->select('k.*, w.name as username');
            $this->db->from('worker as w, kyc as k');
            $this->db->where('w.id = k.user_id and k.user_id='.$id);
            $query = $this->db->get();
            return $query->result();
        }

        public function checkIfUserExists($phone, $type){
            $this->db->select('*');
            $query = $this->db->get_where($type, array('phone' => $phone));
            return $query->num_rows() > 0 ? true : false;
        }

        public function checkUserById($id, $type){
            $this->db->select('*');
            $query = $this->db->get_where($type, array('id' => $id));
            return $query->num_rows() > 0 ? true : false;
        }

        public function getUserByPhone($phone, $type){
            $this->db->select('*');
            $query = $this->db->get_where($type, array('phone' => $phone));
            return $query->num_rows() > 0 ?  $query->row(): false;
        }

        public function checkKYCById($id, $type){
            $this->db->select('*');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->num_rows() > 0 ? true : false;
        }

        public function checkBookingByReqNo($req_no){
            $this->db->select('*');
            $query = $this->db->get_where('booking', array('req_no' => $req_no));
            return $query->num_rows() > 0 ? true : false;
        }

        public function checkIfKYCVerified($id, $type){
            $this->db->select('is_verified');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->row();
        }

        public function checkKYCStepsById($id, $type){
            $this->db->select('steps_filled');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->row();
        }

        public function checkIfBankDetailsExistById($id, $type){
            $this->db->select('*');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->num_rows() > 0 ? true : false;
        }

        public function getBankDetailsById($id, $type){
            $this->db->select('name, ac_no, ifsc_code, bank_cheque');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->num_rows() > 0 ?  $query->row(): false;
        }

        public function getUserAboutById($id, $type){
            $this->db->select('year, month, business, phone, website, intro');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->num_rows() > 0 ?  $query->row(): false;
        }

        public function getAllVerifiedWorkers($profession){
            $this->db->select('w.id, w.name, w.phone, w.primary_profession, w.lat, w.lng');
            $this->db->from('worker as w, kyc as k');
            $this->db->where('w.otp_verified = 1 and k.user_id = w.id and k.is_verified = 1 and w.primary_profession="'.$profession.'"');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllActiveRequests($id){
            $this->db->select('c.name as customer_name, r.id, r.customer_id, r.vendor_id, r.req_no, r.request_status, r.created_at');
            $this->db->from('customer as c, request as r');
            $this->db->where('c.id = r.customer_id and r.vendor_id="'.$id.'"');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllBookings($id){
            $this->db->select('b.customer_id, b.booking_id, b.vendor_id, v.name as vendor_name, v.phone as vendor_phone, v.primary_profession as service, b.amount, b.booking_status, b.created_at');
            $this->db->from('worker as v, booking as b');
            $this->db->where('v.id = b.vendor_id and b.customer_id="'.$id.'"');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllPendingBookings($id){
            $this->db->select('b.customer_id, r.lat as booking_lat, r.lng as booking_lng, b.booking_id, b.vendor_id, v.name as vendor_name, v.phone as vendor_phone, v.primary_profession as service, b.amount, b.booking_status, b.created_at');
            $this->db->from('worker as v, booking as b, request as r');
            $this->db->where('v.id = b.vendor_id and b.customer_id="'.$id.'" and b.booking_status=1 and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllVendorBookings($id){
            $this->db->select('b.customer_id, b.booking_id, b.vendor_id, c.name as customer_name, c.phone as customer_phone, r.lat as booking_lat, r.lng as booking_lng, b.amount, b.booking_status, b.created_at');
            $this->db->from('customer as c, worker as v, booking as b, request as r');
            $this->db->where('b.vendor_id = v.id and v.id = '.$id.' and b.customer_id= c.id and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getBookingInfo($id, $booking_id){
            $this->db->select('b.customer_id, b.booking_id, b.vendor_id, c.name as customer_name, c.phone as customer_phone, r.lat as customer_lat, r.lng as customer_lng, l.lat as vendor_lat, l.lng as vendor_lng, b.amount, b.booking_status, b.created_at');
            $this->db->from('customer as c, worker as v, booking as b, request as r, vendor_booking_location as l');
            $this->db->where('b.vendor_id = v.id and v.id = '.$id.' and l.req_no = b.req_no and b.booking_id = '.$booking_id.' and b.customer_id= c.id and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getBookingInfoByCustomerID($id, $booking_id){
            $this->db->select('b.customer_id, b.booking_id, b.vendor_id, c.name as customer_name, c.phone as customer_phone, r.lat as customer_lat, r.lng as customer_lng, l.lat as vendor_lat, l.lng as vendor_lng, b.amount, b.booking_status, b.created_at');
            $this->db->from('customer as c, worker as v, booking as b, request as r, vendor_booking_location as l');
            $this->db->where('b.vendor_id = v.id and l.req_no = b.req_no and b.booking_id = '.$booking_id.' and b.customer_id= c.id and c.id='.$id.' and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getVendorLocation($booking_id){
            $this->db->select('l.lat as vendor_lat, l.lng as vendor_lng, r.lat as customer_lat, r.lng as customer_lng');
            $this->db->from('booking as b, vendor_booking_location as l, request as r');
            $this->db->where('b.req_no = l.req_no and r.req_no = b.req_no and b.booking_id='.$booking_id);
            $query = $this->db->get();
            return $query->result();
        }

        public function updatebookingrequest($table, $data, $id, $req_no){
            $this->db->where('id', $id);
            $this->db->where('req_no', $req_no);
            $query = $this->db->update($table, $data); 
            return $this->db->affected_rows() > 0 ? true : false;
        }

        public function updatevendorlocation($data, $req_no){
            $this->db->where('req_no', $req_no);
            $this->db->update('vendor_booking_location', $data); 
            return true;
        }

        public function updateBookingStatus($data, $booking_id){
            $this->db->where('booking_id', $booking_id);
            $query = $this->db->update('booking', $data); 
            return $query;
        }

        public function deletebookingrequest($table, $req_no){
            $res = $this->db->delete($table, array('req_no' => $req_no, 'request_status' => 0)); 
            return $res;
        }

        public function deleteEntity($table, $id){
            $res = $this->db->delete($table, array('id' => $id)); 
            return $res;
        }
    
    }

?>