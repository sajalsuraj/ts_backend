<?php

    require APPPATH . 'libraries/ImplementJWT.php';
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Update extends CI_Controller{

        public function __construct(){
            $this->objOfJwt = new ImplementJwt();
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: access_token, Cache-Control');
            header('Access-Control-Allow-Methods: GET, HEAD, POST, PUT, DELETE');
            parent::__construct();
        }

        public function sendsms($msg, $phone){
           
            $msg = rawurlencode($msg);    //Message Here

            $url = "http://sms99.co.in/pushsms.php?username=trjhalakr&password=incorrecthaibhai&sender=webacc&message=".$msg."&numbers=".$phone;  //Store data into URL variable

            // $ret = file($url);    //Call Url variable by using file() function

            // return $ret[0];    //$ret stores the msg-id
            $ch = curl_init();

            curl_setopt_array(
                $ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            ));
                
            $output = curl_exec($ch);
            return $output;

        }

        public function kyc(){
            if($this->user->kyc($_POST['type'], $_POST['status'], $_POST['id'])){  
                echo json_encode(['status' => true, 'message' => "KYC verified"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Not Updated"]);
            }
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

        public function userprofile(){

            if(isset($_FILES["face_photo"])){
                $folder= './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["face_photo"]["name"]);
                $target_file_img = $folder. round(microtime(true)).'face.'.$temp[1]; 
                $_POST['face_photo'] = round(microtime(true)).'face.'.$temp[1];
                move_uploaded_file($_FILES["face_photo"]["tmp_name"], $target_file_img); 
            }
        
            if(isset($_FILES["side_face_photo"])){
                $folder= './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["side_face_photo"]["name"]);
                $target_file_img = $folder. round(microtime(true)).'sideface.'.$temp[1]; 
                $_POST['side_face_photo'] = round(microtime(true)).'sideface.'.$temp[1];
                move_uploaded_file($_FILES["side_face_photo"]["tmp_name"], $target_file_img);
            }

            if(isset($_FILES["full_body_photo"])){
                $folder= './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["full_body_photo"]["name"]);
                $target_file_img = $folder. round(microtime(true)).'fullbody.'.$temp[1]; 
                $_POST['full_body_photo'] = round(microtime(true)).'fullbody.'.$temp[1];
                move_uploaded_file($_FILES["full_body_photo"]["tmp_name"], $target_file_img); 
            }

            if(isset($_FILES["tool_photo"])){
                $folder= './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["tool_photo"]["name"]);
                $target_file_img = $folder. round(microtime(true)).'tool.'.$temp[1]; 
                $_POST['tool_photo'] = round(microtime(true)).'tool.'.$temp[1];
                move_uploaded_file($_FILES["tool_photo"]["tmp_name"], $target_file_img); 
            }
            $id = $_POST['user_id'];
            unset($_POST['user_id']);
            if($this->user->userupdate('worker', $_POST, $id)){  
                echo json_encode(['status' => true, 'message' => "Profile updated successfully"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
            }
        }

        public function userabout(){

            if($this->admin->checkKYCById($_POST['user_id'], 'about')){
                if($this->user->aboutupdate('about', $_POST, $_POST['user_id'])){  
                    echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
                }
                else{
                    echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                }
            }
            else{
                $data = $this->admin->addData($_POST, 'about');
                if($data){  
                    echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
                }
                else{
                    echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                }
            }
            
        }

        public function bookingrequest(){

            $data['request_status'] = $_POST['request_status'];
            if($this->admin->updatebookingrequest('request', $data, $_POST['id'], $_POST['req_no'])){
                if($this->admin->deletebookingrequest('request', $_POST['req_no'])){
                    $postData = array();
                    $postData['req_no'] = $_POST['req_no'];
                    $postData['booking_id'] = rand(10000,99999)."".$_POST['id'];
                    $postData['vendor_id'] = $_POST['vendor_id'];
                    $postData['customer_id'] = $_POST['customer_id'];
                    $postData['booking_status'] = 1;

                    $locationData = array();
                    $locationData['req_no'] = $_POST['req_no'];
                    $locationData['lat'] = $_POST['lat'];
                    $locationData['lng'] = $_POST['lng'];
                    $this->admin->addData($locationData, 'vendor_booking_location');
                    if($this->admin->addData($postData, 'booking')){
                        echo json_encode(['status' => true, 'message' => "Booking created successfully"]);
                    }
                    else{
                        echo json_encode(['status' => false, 'message' => "Error occurred while creating booking"]);
                    }
                }
                else{
                    echo json_encode(['status' => false, 'message' => "Error occurred while removing other booking requests"]);
                }   
            }
            else{
                echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
            }

        }


        //APIs

        public function aboutme(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){

                if($this->admin->checkKYCById($_POST['user_id'], 'about')){
                    if($this->user->aboutupdate('about', $_POST, $_POST['user_id'])){  
                        echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
                    }
                    else{
                        echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                    }
                }
                else{
                    $data = $this->admin->addData($_POST, 'about');
                    if($data){  
                        echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
                    }
                    else{
                        echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                    }
                }
            }
            else{
                echo json_encode(["status" => false,
                "message" => "Unauthorized Access"]);
            }
            
        }

        
        public function profile(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){

                if(isset($_FILES["face_photo"])){
                    $folder= './assets/admin/images/profile/';
                    $temp = explode(".", $_FILES["face_photo"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'face.'.$temp[1]; 
                    $_POST['face_photo'] = round(microtime(true)).'face.'.$temp[1];
                    move_uploaded_file($_FILES["face_photo"]["tmp_name"], $target_file_img); 
                }
            
                if(isset($_FILES["side_face_photo"])){
                    $folder= './assets/admin/images/profile/';
                    $temp = explode(".", $_FILES["side_face_photo"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'sideface.'.$temp[1]; 
                    $_POST['side_face_photo'] = round(microtime(true)).'sideface.'.$temp[1];
                    move_uploaded_file($_FILES["side_face_photo"]["tmp_name"], $target_file_img);
                }

                if(isset($_FILES["full_body_photo"])){
                    $folder= './assets/admin/images/profile/';
                    $temp = explode(".", $_FILES["full_body_photo"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'fullbody.'.$temp[1]; 
                    $_POST['full_body_photo'] = round(microtime(true)).'fullbody.'.$temp[1];
                    move_uploaded_file($_FILES["full_body_photo"]["tmp_name"], $target_file_img); 
                }

                if(isset($_FILES["tool_photo"])){
                    $folder= './assets/admin/images/profile/';
                    $temp = explode(".", $_FILES["tool_photo"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'tool.'.$temp[1]; 
                    $_POST['tool_photo'] = round(microtime(true)).'tool.'.$temp[1];
                    move_uploaded_file($_FILES["tool_photo"]["tmp_name"], $target_file_img); 
                }
                $id = $_POST['user_id'];
                unset($_POST['user_id']);
                if($this->user->userupdate('worker', $_POST, $id)){  
                    $userdata = $this->user->getProfileData($id);
                    if($userdata->face_photo != ""){
                        $userdata->face_photo = base_url()."assets/admin/images/profile/".$userdata->face_photo;
                    }

                    if($userdata->side_face_photo != ""){
                        $userdata->side_face_photo = base_url()."assets/admin/images/profile/".$userdata->side_face_photo;
                    }

                    if($userdata->full_body_photo != ""){
                        $userdata->full_body_photo = base_url()."assets/admin/images/profile/".$userdata->full_body_photo;
                    }

                    if($userdata->tool_photo != ""){
                        $userdata->tool_photo = base_url()."assets/admin/images/profile/".$userdata->tool_photo;
                    }
                    echo json_encode(['status' => true, 'data' => $userdata, 'message' => "Profile updated successfully"]);
                }
                else{
                    echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                }
            }
            else{
                echo json_encode(["status" => false,
                "message" => "Unauthorized Access"]);
            }
        }

        public function otp(){

            if($this->user->userOTPVerified('worker', $_POST['otp_verified'], $_POST['phone'])){
                echo json_encode(['status' => true, 'message' => "User verified"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Error occurred while verification"]);
            }
        }

        public function customerprofile(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                if(isset($_FILES["photo"])){
                    $folder= './assets/admin/images/profile/';
                    $temp = explode(".", $_FILES["photo"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'.'.$temp[1]; 
                    $_POST['photo'] = round(microtime(true)).'.'.$temp[1];
                    move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file_img); 
                }

                $id = $_POST['user_id'];
                unset($_POST['user_id']);
                if($this->user->userupdate('customer', $_POST, $id)){
                    $userdata = $this->customer->getProfileData($id);
                    if($userdata->photo != ""){
                        $userdata->photo = base_url()."assets/admin/images/profile/".$userdata->photo;
                    }
                    echo json_encode(['status' => true, 'data' => $userdata, 'message' => "Profile updated successfully"]);
                }
                else{
                    echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                }
            }
        }

        public function userbookingrequest(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['vendor_id'])){

                if($this->admin->checkBookingByReqNo($_POST['req_no'])){
                    $response = array(
                        "status" => false,
                        "message" => "This booking request is already accepted"
                    );
                }
                else{
                    date_default_timezone_set('Asia/Kolkata');
                    $data['accepted_at'] = time();
                    $data['request_status'] = $_POST['request_status'];
                    if($this->admin->updatebookingrequest('request', $data, $_POST['id'], $_POST['req_no'])){
                        if($this->admin->deletebookingrequest('request', $_POST['req_no'])){
                            $postData = array();
                            $postData['req_no'] = $_POST['req_no'];
                            $postData['booking_id'] = rand(10000,99999)."".$_POST['id'];
                            $postData['vendor_id'] = $_POST['vendor_id'];
                            $postData['customer_id'] = $_POST['customer_id'];
                            $postData['booking_status'] = 1;
                            $postData['created_at'] = time();

                            $locationData = array();
                            $locationData['req_no'] = $_POST['req_no'];
                            $locationData['lat'] = $_POST['lat'];
                            $locationData['lng'] = $_POST['lng'];
                            
                            $this->admin->addData($locationData, 'vendor_booking_location');
                            if($this->admin->addData($postData, 'booking')){
                                $vendor = $this->user->getProfileData($_POST['vendor_id']);
                                $customer = $this->user->getCustomerData($_POST['customer_id']);
                                $api_key = "AAAAQgv_Zag:APA91bGGYsWdrhoPDxgNrNP-FSn30esdz3oyccqMAMXX1ym0Cl7yB6XcAxIr8oWKQ0tDV5hzS0tV5fxduMIlcqTvT2IwjytTCAlzVOEE-K54pvggi0a9DEGxmyVcZfyFDXIVzG5HBuxa";
                                $to = $customer->device_id;
                                $notificationMsg = array
                                (
                                    'body' 	=> $vendor->name.' has accepted your booking request',
                                    'title'	=> 'Troubleshooter',    
                                );
                                $bodyData = array(
                                    'action'=> "request_accepted"
                                );
                                $this->send_notification($api_key, $to, $notificationMsg, $bodyData);
                                $response = array('status' => true, 'message' => "Booking created successfully");
                            }
                            else{
                                $response = array('status' => false, 'message' => "Error occurred while creating booking");
                            }
                        }
                        else{
                            $response = array('status' => false, 'message' => "Error occurred while removing other booking requests");
                        }   
                    }
                    else{
                        $response = array('status' => false, 'message' => "Error occurred while updating");
                    }
                }
            }
            else{
                if($this->admin->checkUserById($_POST['vendor_id'], 'worker')){
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
                $data['lat'] = $_POST['lat'];
                $data['lng'] = $_POST['lng'];
                if($this->admin->updatevendorlocation($data, $_POST['req_no'])){
                    $response = array('status' => true, 'message' => "Location updated");
                }
                else{
                    $response = array('status' => false, 'message' => "Error occurred while updating");
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

        public function bookingstatus(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data['booking_status'] = $_POST['booking_status'];
                if($this->admin->updateBookingStatus($data, $_POST['booking_id'])){
                    $message = "";
                    if($_POST['booking_status'] == 2){
                        $message = "Task started/In Progress";
                    }
                    else if($_POST['booking_status'] == 3){
                        $message = "Task paused";
                    }
                    else if($_POST['booking_status'] == 4){
                        $message = "Booking Cancelled";
                    }
                    else if($_POST['booking_status'] == 5){
                        $message = "Task completed";
                    }
                    $response = array('status' => true, 'message' => $message);
                }
                else{
                    $response = array('status' => false, 'message' => "Error occurred while updating");
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

        public function customerbookingstatus(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data['booking_status'] = $_POST['booking_status'];
                if($this->admin->updateBookingStatus($data, $_POST['booking_id'])){
                    $message = "";
                    $booking_detail = $this->admin->getBookingInfoByCustomerID($_POST['user_id'], $_POST['booking_id']);
                    $vendor = $this->user->getProfileData($booking_detail[0]->vendor_id);
                    $apiKey = "AAAA0W6cR-g:APA91bGr4S_9LPdoWVc9k3aY5_6Nh3e_orRbsj6dLOq59nAC5GmLS9-21Au2figrAoCu9VjrsgWsd3taKiPvj2s2-niwWGDGA0B5KGGjFdCCZMQMcKdelOcexyXyuNcmcm_iRW9qGEJr";
                    if($_POST['booking_status'] == 2){
                        $message = "Task started/In Progress";
                    }
                    else if($_POST['booking_status'] == 3){
                        $message = "Task paused";
                    }
                    else if($_POST['booking_status'] == 4){
                        $to = $vendor->device_id;
                        $notificationMsg = array
                        (
                            'body' 	=> $booking_detail[0]->customer_name.' has cancelled the booking!!',
                            'title'	=> '[Troubleshooter]:Booking Cancelled!',    
                        );
                        $bodyData = array(
                            'action'=> "booking_cancelled"
                        );
                        $this->send_notification($apiKey, $to, $notificationMsg, $bodyData);
                        $message = "Booking Cancelled";
                    }
                    else if($_POST['booking_status'] == 5){
                        $message = "Task completed";
                    }
                    $response = array('status' => true, 'message' => $message);
                }
                else{
                    $response = array('status' => false, 'message' => "Error occurred while updating");
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

        public function vendortokenupdate(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                if($this->user->userupdate("worker", array("device_id"=>$_POST['token']), $_POST['user_id'])){
                    $response = array(
                        "status" => true,
                        "message" => "Token updated successfully"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Error occurred while updating token, kindly restart the app"
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

        public function customerinfo(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $id = $_POST['user_id'];
                unset($_POST['user_id']);
                if($this->user->userupdate('customer', $_POST, $id)){  
                    $response = array(
                        "status" => true,
                        "message" => "User info updated"
                    );
                }
                else{
                    $response = array(
                        "status" => true,
                        "message" => "Error occurred while updating"
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

        public function vendorpassword(){
            $phone = $_POST['phone'];
            unset($_POST['phone']);
            $_POST['password'] = md5($_POST['password']);
            if($this->user->userupdatebyphone('worker', $_POST, $phone)){
                
                $user = $this->admin->getUserByPhone($phone, "worker");
                $msg = "Your password has been updated successfully";
            
                $headers = "From: noreply@troubleshooters.services". "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                // send email
                
                $mail = mail($user->email,"Password updated - Troubleshooters",$msg, $headers);
                
                if(!$mail) {   
                    $response = array(
                        "status" => true,
                        "message" => "Password updated, error occurred while sending email"
                    );
                } else {
                    $response = array(
                        "status" => true,
                        "message" => "Password updated, email sent to the vendor"
                    );
                }
                $this->sendsms($msg, $phone);
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating password"
                );
            }
            echo json_encode($response);
        }

        public function customerpassword(){
            $phone = $_POST['phone'];
            unset($_POST['phone']);
            $_POST['password'] = md5($_POST['password']);
            if($this->user->userupdatebyphone('customer', $_POST, $phone)){

                $user = $this->admin->getUserByPhone($phone, "customer");
                $msg = "Your password has been updated successfully";
            
                $headers = "From: noreply@troubleshooters.services". "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                // send email
                
                $mail = mail($user->email,"Password updated - Troubleshooters",$msg, $headers);
                
                if(!$mail) {   
                    $response = array(
                        "status" => true,
                        "message" => "Password updated, error occurred while sending email"
                    );
                } else {
                    $response = array(
                        "status" => true,
                        "message" => "Password updated, email sent to the user"
                    );
                }
                $this->sendsms($msg, $phone);
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating password"
                );
            }
            echo json_encode($response);
        }

        public function service(){
            $service_id = $_POST['id'];
            unset($_POST['id']);
            if($this->user->userupdate('services', $_POST, $service_id)){
                $response = array(
                    "status" => true,
                    "message" => "Service Updated"
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating service"
                );
            }
            echo json_encode($response);
        }

        public function contact(){
            $id = $_POST['id'];
            unset($_POST['id']);
            if($this->user->userupdate('contact', $_POST, $id)){
                $response = array(
                    "status" => true,
                    "message" => "Contact us updated"
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating contact"
                );
            }
            echo json_encode($response);
        }

        public function banner(){
            $banner_id = $_POST['id'];
            unset($_POST['id']);
            if($this->user->userupdate('banner', $_POST, $banner_id)){
                $response = array(
                    "status" => true,
                    "message" => "Banner status Updated"
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating banner status"
                );
            }
            echo json_encode($response);
        }

        public function city(){
            $banner_id = $_POST['id'];
            unset($_POST['id']);
            if($this->user->userupdate('city', $_POST, $banner_id)){
                $response = array(
                    "status" => true,
                    "message" => "City name updated"
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating city name"
                );
            }
            echo json_encode($response);
        }

        public function static(){
            if($this->user->staticupdate('static', $_POST, $_POST['type'])){
                $response = array(
                    "status" => true,
                    "message" => "Data updated"
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating"
                );
            }
            echo json_encode($response);
        }
    }