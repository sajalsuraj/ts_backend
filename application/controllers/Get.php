<?php
    require APPPATH . 'libraries/ImplementJWT.php';
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Get extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->objOfJwt = new ImplementJwt();
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: access_token, Cache-Control, Content-Type');
            header('Access-Control-Allow-Methods: GET, HEAD, POST, PUT, DELETE');
        }

        public function send_notification($apiKey, $to, $notification, $data)
        {
            $fields = array
            (
                'to' => $to,
                'notification'	=> $notification,
                "priority" => "high",
                "data" => $data
            );
            
            
            $headers = array
            (
                'Authorization: key='.$apiKey,
                'Content-Type: application/json'
            );
        #Send Reponse To FireBase Server	
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                curl_exec($ch );
                curl_close( $ch );
        }

        public function adminLogin(){   

            $_POST['password'] = md5($_POST['password']);

            $data = $this->admin->login($_POST);  

            if($data){

                $newdata = array(
                    'name'  =>  $data->name,
                    'user_id'     => $data->id,
                    'type' => $data->type 
                );

                $this->session->set_userdata($newdata);  
                echo json_encode(['status' => true, 'message' => 'Successful Login']);

            }
            else{
                echo json_encode(['status' => false, 'message' => 'Unsuccessful Login']);
            }
        
        }//end-function

        //APIs
        public function userlogin(){

            $post = json_decode($this->security->xss_clean($this->input->raw_input_stream), true);
            $post['password'] = md5($post['password']);

            if($this->admin->checkIfUserExists($post['phone'], 'worker')){
                $data = $this->user->login($post);
    
                if($data){
                    $newdata = array(
                        'name'  =>  $data->name,
                        'user_id'     => $data->id,
                        'type' => $data->type 
                    );
                    $jwtToken = $this->objOfJwt->GenerateToken($newdata);
                    echo json_encode(['status' => true, 'access_token'=>$jwtToken, 'message' => 'Successful Login']);
    
                }
                else{
                    echo json_encode(['status' => false, 'message' => 'Unsuccessful Login']);
                }
            }
            else{
                echo json_encode(['status' => false, 'message' => "User doesn't exist"]);
            }
        }

        public function profiledata(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            if(isset($tokenData['user_id'])){
                $data = $this->user->getProfileData($tokenData['user_id']);
                if($data != NULL){
                    if(!$this->admin->checkKYCById($tokenData['user_id'], 'kyc')){
                        $data->is_verified = false;
                    }
                    else{
                        $is_verified = $this->admin->checkIfKYCVerified($tokenData['user_id'], 'kyc');
                        if($is_verified->is_verified == 1){
                            $data->is_verified = true;
                        }
                        else{
                            $data->is_verified = false;
                        }  
                    }
                    if($data->face_photo != ""){
                        $data->face_photo = base_url()."assets/admin/images/profile/".$data->face_photo;
                    }

                    if($data->side_face_photo != ""){
                        $data->side_face_photo = base_url()."assets/admin/images/profile/".$data->side_face_photo;
                    }

                    if($data->full_body_photo != ""){
                        $data->full_body_photo = base_url()."assets/admin/images/profile/".$data->full_body_photo;
                    }

                    if($data->tool_photo != ""){
                        $data->tool_photo = base_url()."assets/admin/images/profile/".$data->tool_photo;
                    }
                    
                    echo json_encode(["data" => $data, "status" => true]);
                }
                else{
                    echo json_encode(["status" => false, "message" => "No data available"]);
                }
            }
            else{
                echo json_encode(["status" => false, "message" => "Access token missing in the header"]);
            }
            
        } 

        public function bankdetails(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                if(!$this->admin->getBankDetailsById($_POST['user_id'], 'bank_details')){
                    $response = array(
                        "status" => false,
                        "message" => "Bank Details doesn't exist for this user"
                    );
                }
                else{
                    $bank_detail = $this->admin->getBankDetailsById($_POST['user_id'], 'bank_details');
                    if($bank_detail->bank_cheque != ""){
                        $bank_detail->bank_cheque = base_url()."assets/admin/images/bank_cheque/".$bank_detail->bank_cheque;
                    }
                    $response = array(
                        "status" => true,
                        "message" => "Bank Details Exist",
                        "data" => $bank_detail
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'worker')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
                
            }
            echo json_encode($response);
        }

        public function aboutme(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                if(!$this->admin->getUserAboutById($_POST['user_id'], 'about')){
                    $response = array(
                        "status" => false,
                        "message" => "About section has not been added yet, please update your about section"
                    );
                }
                else{
                    $about = $this->admin->getUserAboutById($_POST['user_id'], 'about');
                    $response = array(
                        "status" => true,
                        "message" => "About section",
                        "data" => $about
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'worker')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
                
            }
            echo json_encode($response);
        }

        public function kycdetails(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                if(!$this->admin->checkKYCById($_POST['user_id'], 'kyc')){
                    $response = array(
                        "status" => false,
                        "message" => "KYC Details doesn't exist for this user"
                    );
                }
                else{
                    $kyc_detail = $this->admin->getKycByID($_POST['user_id']);
                    if($kyc_detail[0]->img_front_side != ""){
                        $kyc_detail[0]->img_front_side = base_url()."assets/admin/images/documents/".$kyc_detail[0]->img_front_side;
                    }

                    if($kyc_detail[0]->img_back_side != ""){
                        $kyc_detail[0]->img_back_side = base_url()."assets/admin/images/documents/".$kyc_detail[0]->img_back_side;
                    }

                    unset($kyc_detail[0]->id);
                    unset($kyc_detail[0]->declaration);
                
                    $response = array(
                        "status" => true,
                        "message" => "KYC Exist",
                        "data" => $kyc_detail
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'worker')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
                
            }
            echo json_encode($response);
        }

        public function verifyotp(){
            
            if($this->user->checkUserOtp($_POST['phone'], $_POST['otp'], 'otp')){
                $POST['otp_verified'] = 1;
                $phone = $_POST['phone'];
                unset($_POST['phone']);
                if($this->user->updateUserIfVerified('worker', $POST, $phone)){
                    if($this->user->deleteOTP('otp', $phone)){
                        echo json_encode(['status' => true, 'message' => "User verified"]);
                    }
                }
            }
            else{
                echo json_encode(['status' => false, 'message' => "OTP didn't match"]);
            }
        }

        public function allservicerequest(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){

                $data = $this->admin->getAllActiveRequests($_POST['user_id']);
                if($data){
                    $response = array(
                        "status" => true,
                        "message" => "Requests available",
                        "bookings" => $data
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Requests not available"
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'worker')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
            echo json_encode($response);
        }

        public function allorders(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){

                $data = $this->admin->getAllVendorBookings($_POST['user_id']);
                if($data){
                    $response = array(
                        "status" => true,
                        "message" => "Order List",
                        "bookings" => $data
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Orders not available"
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'worker')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
            echo json_encode($response);
        }

        public function bookinginfo(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getBookingInfo($_POST['user_id'], $_POST['booking_id']);
                if($data){
                    $response = array(
                        "status" => true,
                        "message" => "Booking data",
                        "info" => $data
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Booking not available"
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'worker')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
            echo json_encode($response);
        }

        //Customer APIs

        public function verifyotpcustomer(){
            
            if($this->user->checkUserOtp($_POST['phone'], $_POST['otp'], 'otp')){
                $POST['otp_verified'] = 1;
                $phone = $_POST['phone'];
                unset($_POST['phone']);
                if($this->user->updateUserIfVerified('customer', $POST, $phone)){
                    if($this->user->deleteOTP('otp', $phone)){
                        echo json_encode(['status' => true, 'message' => "User verified"]);
                    }
                }
            }
            else{
                echo json_encode(['status' => false, 'message' => "OTP didn't match"]);
            }
        }

        public function customerlogin(){

            //$_POST = json_decode($this->security->xss_clean($this->input->raw_input_stream), true);
            $_POST['password'] = md5($_POST['password']);

            if($this->admin->checkIfUserExists($_POST['phone'], 'customer')){
                $data = $this->customer->login($_POST);
    
                if($data){

                    $this->user->userupdate("customer",array("device_id" => $_POST['device_id']),$data->id);
                    $newdata = array(
                        'name'  =>  $data->name,
                        'user_id'     => $data->id,
                        'phone' => $data->phone
                    );
                    $jwtToken = $this->objOfJwt->GenerateToken($newdata);
                    echo json_encode(['status' => true, 'access_token'=>$jwtToken, 'user_id'=> $data->id, 'message' => 'Successful Login']);
    
                }
                else{
                    echo json_encode(['status' => false, 'message' => 'Unsuccessful Login']);
                }
            }
            else{
                echo json_encode(['status' => false, 'message' => "User doesn't exist"]);
            }
        }

        public function services(){
            $data = $this->admin->getAllServices();

            if($data){
                echo json_encode(['status' => true, 'data' => $data['result'], 'message' => 'Service list']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Services not available']);
            }
        }
        function distance($lat1, $lon1, $lat2, $lon2, $unit) {
            if (($lat1 == $lat2) && ($lon1 == $lon2)) {
                return 0;
            }
            else {
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $unit = strtoupper($unit);
            
                if ($unit == "K") {
                return ($miles * 1.609344);
                } else if ($unit == "N") {
                return ($miles * 0.8684);
                } else {
                return $miles;
                }
            }
        }
        public function nearbyvendor(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $allVendors = $this->admin->getAllVerifiedWorkers($_POST['profession']);
                $flag = false;
                $reqNo = rand(10000,99999);
                foreach($allVendors as $key => $value){
                    $dist = $this->distance((float) $_POST['lat'], (float) $_POST['lng'], (float) $value->lat, (float) $value->lng, "K");
                    if($dist < 10){
                        //$nearest_vendors[] = $value;
                        $post = array();
                        $post['vendor_id'] = $value->id;
                        $post['req_no'] = $reqNo."".$_POST['user_id'];
                        $post['request_status'] = 0;
                        $post['customer_id'] = $_POST['user_id'];
                        $post['lat'] = $_POST['lat'];
                        $post['lng'] = $_POST['lng'];
                        date_default_timezone_set('Asia/Kolkata');
                        $post['created_at'] = time();
                        $this->admin->addData($post, 'request');
                        $vendor = $this->user->getProfileData($value->id);
                        $notificationMessage = array();
                        $notificationMessage = array(
                            "title" => "Troubleshooter",
                            "body" => "There is a booking request!!"
                        );
                        $bodyData = array(
                            'action'=> "booking_request"
                        );
                        $api_key = "AAAA0W6cR-g:APA91bGr4S_9LPdoWVc9k3aY5_6Nh3e_orRbsj6dLOq59nAC5GmLS9-21Au2figrAoCu9VjrsgWsd3taKiPvj2s2-niwWGDGA0B5KGGjFdCCZMQMcKdelOcexyXyuNcmcm_iRW9qGEJr";
                        $to = $vendor->device_id;
                        $this->send_notification($api_key, $to, $notificationMessage, $bodyData);
                        $flag = true;
                    }
                }

                if($flag){
                    $response = array(
                        "status" => true,
                        "message" => "Your request has been sent to nearby vendors. Your booking will be confirmed once your request gets accepted."
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Nearby vendors are not available. Try again later."
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'customer')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
            echo json_encode($response);
            
        }

        public function userbookings(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getAllBookings($_POST['user_id']);
                if($data){
                    $response = array(
                        "status" => true,
                        "message" => "Bookings available",
                        "bookings" => $data
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Bookings not available"
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'customer')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
            echo json_encode($response);
        }

        public function pendinguserbookings(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getAllPendingBookings($_POST['user_id']);
                if($data){
                    $response = array(
                        "status" => true,
                        "message" => "Bookings available",
                        "bookings" => $data
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Bookings not available"
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'customer')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
            echo json_encode($response);
        }

        public function vendorlocation(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getVendorLocation($_POST['booking_id']);
                if($data){
                    $dist = $this->distance((float) $data[0]->customer_lat, (float) $data[0]->customer_lng, (float) $data[0]->vendor_lat, (float) $data[0]->vendor_lng, "K");
                    $locData = $data[0];
                    $locData->distance = round($dist, 3) * 1000;
                    $response = array(
                        "status" => true,
                        "message" => "Location status",
                        "location" => $locData
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Location not available"
                    );
                }
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'customer')){
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
            echo json_encode($response);

        }

    }

    