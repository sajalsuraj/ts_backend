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

        public function getTerms($type){
            $query = $this->db->get_where('static', array('type' => $type));
            return $query->row();
        }

        public function getReferralAmount(){
            $query = $this->db->get_where('referral');
            return $query->row();
        }

        public function getHomePage(){
            $query = $this->db->get('homepage');
            return $query->row();
        }

        public function getContact(){
            $query = $this->db->get('contact');
            return $query->row();
        }

        public function getAllRequests(){
            $this->db->select('c.name as customer_name, r.services, r.vendor_id, w.name as vendor_name, r.req_no, r.lat, r.lng, r.last_assigned_to, r.request_status, r.accepted_at, r.created_at');
            $this->db->from('customer as c, request as r, worker as w');
            $this->db->where('c.id = r.customer_id and r.vendor_id = w.id');
            $this->db->order_by('r.id','DESC');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllNotifications(){
            $this->db->select('n.*, w.id as vendor_id, w.name as vendor_name');
            $this->db->from('admin_notification as n, worker as w');
            $this->db->where('n.vendor_id = w.id');
            $this->db->order_by('n.vendor_id','DESC');
            $query = $this->db->get();
            return $query->result();
        }

        public function checkIfOTPVerified($id, $type){
            $this->db->select('otp_verified');
            $query = $this->db->get_where($type, array('id' => $id));
            return $query->row();
        }

        public function getAdminProfile($id){
            $this->db->select('*');
            $query = $this->db->get_where("worker", array('id' => $id));
            return $query->row();
        }

        public function getAwards($id){
            $this->db->select('*');
            $query = $this->db->get_where('award', array('user_id' => $id));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllFAQTitle(){
            $this->db->select('*');
            $query = $this->db->get('faq_title');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllPackages(){
            $this->db->select('p.*');
            $this->db->from('packages as p');
            $query = $this->db->get();
            return $query->result();  
        }

        public function getAllMemberships(){
            $this->db->select('p.*');
            $this->db->from('membership as p');
            $query = $this->db->get();
            return $query->result();  
        }

        public function membershipById($id){
            $this->db->select('*');
            $query = $this->db->get_where('membership', array('id' => $id));
            return $query->row();
        }

        public function faqById($id){
            $this->db->select('*');
            $query = $this->db->get_where('faq', array('id' => $id));
            return $query->row();
        }

        public function faqTitleById($id){
            $this->db->select('*');
            $query = $this->db->get_where('faq_title', array('id' => $id));
            return $query->row();
        }

        public function packageById($id){
            $this->db->select('*');
            $query = $this->db->get_where('packages', array('id' => $id));
            return $query->row();
        }

        public function checkIfUserHasMembership($userid){
            $this->db->select('*');
            $query = $this->db->get_where('membership_users', array('user_id' => $userid));
            return $query->result();
        }

        public function checkIfUserHasPackage($userid){
            $this->db->select('*');
            $query = $this->db->get_where('package_users', array('user_id' => $userid));
            return $query->result();
        }

        public function checkIfUserBoughtMembership($mid, $userid){
            $this->db->select('*');
            $query = $this->db->get_where('membership_users', array('user_id' => $userid, "membership_id"=>$mid));
            return $query->num_rows();
        }

        public function checkIfUserBoughtPackage($pid, $userid){
            $this->db->select('*');
            $query = $this->db->get_where('package_users', array('user_id' => $userid, "package_id"=>$pid));
            return $query->num_rows();
        }

        // public function getAllMemberships(){
        //     $this->db->select('p.name as package_name, c.name as customer_name, m.bought_on, m.expiring_on, m.created_at');
        //     $this->db->from('packages as p, membership as m, customer as c');
        //     $this->db->where('p.id = m.package_id and m.customer_id = c.id');
        //     $query = $this->db->get();
        //     return $query->result(); 
        // }

        public function getAwardsCount($id){
            $this->db->select('count(*) as total_awards');
            $query = $this->db->get_where('award', array('user_id' => $id));
            return $query->num_rows() > 0 ? $query->row(): 0;
        }

        public function getVendorRating($id){
            $this->db->select('(sum(service_quality)/count(*)) as service_quality_rating, (sum(behaviour)/count(*)) as behaviour_rating, (sum(speed_of_work)/count(*)) as speed_of_work_rating');
            $query = $this->db->get_where('rating', array('vendor_id' => $id));
            return $query->num_rows() > 0 ? $query->row(): 0;
        }

        public function getTrainingVideoCount(){
            $this->db->select('count(*) as total_videos');
            $query = $this->db->get('training');
            return $query->num_rows() > 0 ? $query->row(): 0;
        }

        public function getMaxTrainingNumber(){
            $this->db->select('max(video_no) as max_video_num');
            $query = $this->db->get('training');
            return $query->num_rows() > 0 ? $query->row(): 0;
        }

        public function checkIfTrainingVideoAvailable($vid_no){
            $this->db->select('*');
            $query = $this->db->get_where('training', array('video_no' => $vid_no));
            return $query->num_rows() > 0 ? true: false;
        }

        public function isAwardAvailable($id){
            $this->db->select('*');
            $query = $this->db->get_where('award', array('user_id' => $id));
            return $query->num_rows() > 0 ? true: false;
        }

        public function last_record($field, $table)
        { 
            return $this->db->select($field)->from($table)->limit(1)->order_by($field,'DESC')->get()->row();
        } 

        public function getAllWorkers(){
            $query = $this->db->get_where('worker', array('type' => 'worker'));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllCustomers(){
            $query = $this->db->get('customer');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllServices(){
            $query = $this->db->get('services');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllTrainingVideos(){
            $this->db->from('training');
            $this->db->order_by("video_no", "asc");
            $query = $this->db->get();
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllBanners(){
            $query = $this->db->get('banner');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllCities(){
            $query = $this->db->get('city');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllVehicles(){
            $query = $this->db->get('vehicle');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllFAQTitles(){
            $query = $this->db->get('faq_title');
            $data['result'] = $query->result();
            return $data; 
        }

        public function getActivatedBanners(){
            $this->db->select('*');
            $query = $this->db->get_where("banner", array("status"=>'true'));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getVendorNotification($id){
            $this->db->select('*');
            $query = $this->db->order_by('created_at', 'desc')->get_where("admin_notification", array("vendor_id"=>$id));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getServicesExceptLevel3(){
            $this->db->select('*');
            $query = $this->db->get_where("services", array("level!="=>'3'));
            $data['result'] = $query->result();
            return $data; 
        }

        public function getCommentsByBookingId($booking_id){
            $this->db->select('*');
            $query = $this->db->get_where("comments", array("booking_id="=>$booking_id));
            $data['result'] = $query->result();
            return $data;
        }

        public function getServicesLevelWise($level){
            $this->db->select('*');
            $query = $this->db->get_where("services", array("level="=>$level));
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

        public function getAllParentServicesWithLimit($limit){
            $query = $this->db->get_where('services', array('parent_category' => ''), $limit);
            $data['result'] = $query->result();
            return $data; 
        }

        public function getAllKYC(){
            $this->db->select('k.*, w.id as vendor_id, w.mode_of_transport as vehicle, w.face_photo as face_photo, w.side_face_photo as side_face_photo, w.full_body_photo as full_body_photo, w.tool_photo as tool_photo, w.name as username');
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
            $this->db->select('id_type, is_verified');
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
            $this->db->select('name, ac_no, ifsc_code, bank_cheque, bank_name');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->num_rows() > 0 ?  $query->row(): false;
        }

        public function getUserAboutById($id, $type){
            $this->db->select('year, month, business, phone, website, intro');
            $query = $this->db->get_where($type, array('user_id' => $id));
            return $query->num_rows() > 0 ?  $query->row(): false;
        }

        public function getAllFAQs(){
            $this->db->select('faq.*, fTitle.title');
            $this->db->from('faq, faq_title as fTitle');
            $this->db->where('faq.faq_title = fTitle.id');
            $query = $this->db->get();
            return $query->result();
        }

        public function isUserInfoAvailable($id){
            $this->db->select('*');
            $query = $this->db->get_where('about', array('user_id' => $id));
            return $query->num_rows() > 0 ? true: false;
        }

        public function getAllVerifiedWorkers($profession){
            $this->db->select('w.id, w.name, w.phone, w.primary_profession, w.lat, w.lng');
            $this->db->from('worker as w, kyc as k');
            $this->db->where('w.otp_verified = 1 and k.user_id = w.id and k.is_verified = 1 and w.sub_profession REGEXP REPLACE("'.$profession.'", ",", "(\,|$)|")');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllActiveRequests($id){
            $this->db->select('c.name as customer_name, r.id, r.customer_id, r.services, r.vendor_id, r.req_no, r.request_status, r.created_at');
            $this->db->from('customer as c, request as r');
            $this->db->where('c.id = r.customer_id and r.vendor_id="'.$id.'" and r.request_status <> 2');
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

        public function getAllBookingsDashboard(){
            $this->db->select('c.name as customer_name, b.req_no, b.has_paid, b.started_at, b.paused_at, b.restarted_at, b.completed_at, b.booking_otp as booking_otp, b.is_otp_verified as is_otp_verified, b.booking_id, v.name as vendor_name, v.id as vendor_id, b.reached_location_at as reached_location_at, b.reason_to_cancel, b.amount, b.booking_status, b.created_at');
            $this->db->from('worker as v, booking as b, request as r, customer as c');
            $this->db->where('v.id = b.vendor_id and b.customer_id=c.id and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllPendingBookings($id){
            $this->db->select('b.customer_id, r.lat as booking_lat, r.lng as booking_lng, b.booking_otp as booking_otp, b.is_otp_verified as is_otp_verified, b.booking_id, b.vendor_id, v.name as vendor_name, v.phone as vendor_phone, b.reached_location_at as reached_location_at, r.services as services, b.amount, b.booking_status, b.created_at');
            $this->db->from('worker as v, booking as b, request as r');
            $this->db->where('v.id = b.vendor_id and b.customer_id="'.$id.'" and (b.booking_status=1 or b.booking_status=2 or b.booking_status=3 or b.booking_status=6) and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllCompletedBookings($id){
            $this->db->select('b.customer_id, r.lat as booking_lat, r.lng as booking_lng, b.booking_id, b.vendor_id, v.name as vendor_name, v.phone as vendor_phone, b.reached_location_at as reached_location_at, r.services as services, b.amount, b.booking_status, b.has_paid, b.created_at');
            $this->db->from('worker as v, booking as b, request as r');
            $this->db->where('v.id = b.vendor_id and b.customer_id="'.$id.'" and b.booking_status=5 and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllVendorBookings($id){
            $this->db->select('b.customer_id, b.booking_id, b.vendor_id, c.name as customer_name, c.phone as customer_phone, r.lat as booking_lat, r.services as services, r.lng as booking_lng, b.amount, b.booking_status, b.created_at');
            $this->db->from('customer as c, worker as v, booking as b, request as r');
            $this->db->where('b.vendor_id = v.id and v.id = '.$id.' and b.customer_id= c.id and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function bookingtimeline($booking_id){
            $this->db->select('w.name as vendor_name, r.services as services, r.created_at as requested_at, r.accepted_at as accepted_at, b.reached_location_at as reached_location_at, b.started_at as job_started_at, b.paused_at as job_paused_at, b.restarted_at as job_restarted_at, b.completed_at as job_completed_at');
            $this->db->from('worker as w, booking as b, request as r');
            $this->db->where('r.vendor_id = w.id and r.req_no = b.req_no and b.booking_id='.$booking_id);
            $query = $this->db->get();
            return $query->result();
        }

        public function getBookingInfo($id, $booking_id){
            $this->db->select('b.customer_id, b.booking_id, b.vendor_id, b.reached_location_at, b.booking_otp, b.is_otp_verified, c.name as customer_name, r.services as services, c.phone as customer_phone, r.lat as customer_lat, r.lng as customer_lng, l.lat as vendor_lat, l.lng as vendor_lng, b.amount, b.booking_status, b.started_at as work_started_at, b.completed_at as work_completed_at, b.service_charge_added, b.created_at');
            $this->db->from('customer as c, worker as v, booking as b, request as r, vendor_booking_location as l');
            $this->db->where('b.vendor_id = v.id and v.id = '.$id.' and l.req_no = b.req_no and b.booking_id = '.$booking_id.' and b.customer_id= c.id and r.req_no = b.req_no');
            $query = $this->db->get();
            return $query->result();
        }

        public function getBookingInfoByCustomerID($id, $booking_id){
            $this->db->select('b.customer_id, b.package, b.membership, b.booking_id, b.vendor_id, b.reached_location_at, b.is_otp_verified, c.name as customer_name, r.services as services, c.phone as customer_phone, r.lat as customer_lat, r.lng as customer_lng, l.lat as vendor_lat, l.lng as vendor_lng, b.amount, b.booking_status, b.started_at as work_started_at, b.completed_at as work_completed_at, b.bill_amount as bill_amount, b.service_charge_added, b.created_at');
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

        public function getServicesFromBooking($booking_id){
            $this->db->select('r.services, b.started_at, b.completed_at, b.bill_amount');
            $this->db->from('booking as b, request as r');
            $this->db->where('b.req_no = r.req_no and b.booking_id='.$booking_id);
            $query = $this->db->get();
            return $query->result();
        }

        public function getServiceRate($service_id){
            $this->db->select('rate_per_min');
            $query = $this->db->get_where('services', array('id' => $service_id));
            return $query->row();
        }

        public function getBookingByID($booking_id){
            $this->db->select('*');
            $query = $this->db->get_where('booking', array('booking_id' => $booking_id));
            return $query->row();
        }

        public function getAllBookingWithStatus1(){
            $this->db->select('*');
            $this->db->from('booking');
            $this->db->where('booking_status < 2 and reached_location_at = ""');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllBookingAfterReachingLocation(){
            $this->db->select('*');
            $this->db->from('booking');
            $this->db->where('booking_status < 2 and reached_location_at <> ""');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllRequestWithStatus0(){
            $this->db->select('*');
            $this->db->from('request');
            $this->db->where('request_status=0');
            $query = $this->db->get();
            return $query->result();
        }

        public function getAllVerifiedVendorsExceptAssigned($profession, $ids){
            $this->db->select('w.id, w.name, w.phone, w.primary_profession, w.lat, w.lng');
            $this->db->from('worker as w, kyc as k');
            $this->db->where('w.otp_verified = 1 and k.user_id = w.id and k.is_verified = 1 and w.primary_profession="'.$profession.'" and w.id not in ('.$ids.')');
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

        public function updateVendorReachTime($data, $booking_id){
            $this->db->where('booking_id', $booking_id);
            $this->db->update('booking', $data); 
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

        public function deletefaqcontentbytitle($id){
            $res = $this->db->delete("faq", array('faq_title' => $id)); 
            return $res;
        }

        public function deleteEntity($table, $id){
            $res = $this->db->delete($table, array('id' => $id)); 
            return $res;
        }
    
    }

?>