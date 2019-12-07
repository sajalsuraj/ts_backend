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

        public function otp($otp, $phone){
           
            $msg = rawurlencode("Verify your account. OTP-".$otp);    //Message Here

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
                    $this->user->userupdate("worker",array("device_id" => $post['device_id']),$data->id);
                    $newdata = array(
                        'name'  =>  $data->name,
                        'user_id'     => $data->id,
                        'type' => $data->type 
                    );
                    $jwtToken = $this->objOfJwt->GenerateToken($newdata);
                    if($this->admin->checkKYCById($data->id, "kyc")){

                        $is_verified = $this->admin->checkIfKYCVerified($data->id, 'kyc');
                        if($is_verified->is_verified == 1){
                            $success_resp = array(
                                'status' => true,
                                'access_token'=>$jwtToken,
                                'message' => 'Successful Login',
                                'is_kyc_verified' => true,
                                'is_kyc_available' => true
                            );
                        }
                        else{
                            $success_resp = array(
                                'status' => true,
                                'access_token'=>$jwtToken,
                                'message' => 'Successful Login',
                                'is_kyc_verified' => false,
                                'is_kyc_available' => true
                            );
                        }
                    }
                    else{
                        $success_resp = array(
                            'status' => true,
                            'access_token'=>$jwtToken,
                            'message' => 'Successful Login',
                            'is_kyc_verified' => false,
                            'is_kyc_available' => false
                        );
                    }
                    echo json_encode($success_resp);
    
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

                        $user = $this->admin->getUserByPhone($phone, "worker");
                        $msg = "Congratulations, you are successfully registered with Troubleshooters Services";
            
                        $headers = "From: noreply@troubleshooters.services". "\r\n" .
                                    'X-Mailer: PHP/' . phpversion();
                        // send email
                        
                        $mail = mail($user->email,"Registration successful with Troubleshooters Services",$msg, $headers);
                        
                        if(!$mail) {   
                            echo json_encode(['status'=> true, 'message' => "User verified, error occurred while sending email"]);   
                        } else {
                            
                            echo json_encode(['status'=> true, 'message' => "User verified, Email sent successfully to the user"]);
                        }
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
                        $user = $this->admin->getUserByPhone($phone, "customer");
                        $msg = "Congratulations, you are successfully registered with Troubleshooters Services";
            
                        $headers = "From: noreply@troubleshooters.services". "\r\n" .
                                    'X-Mailer: PHP/' . phpversion();
                        // send email
                        
                        $mail = mail($user->email,"Registration successful with Troubleshooters Services",$msg, $headers);
                        
                        if(!$mail) {   
                            echo json_encode(['status'=> true, 'message' => "User verified, error occurred while sending email"]);   
                        } else {
                            
                            echo json_encode(['status'=> true, 'message' => "User verified, Email sent successfully to the user"]);
                        }
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
            $data = $this->admin->getAllParentServices();

            if($data){
                echo json_encode(['status' => true, 'data' => $data['result'], 'message' => 'Service list']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Services not available']);
            }
        }

        public function subcategories(){
            $data = $this->admin->checkIfServiceIsParent($_POST['id']);
            if($data){
                echo json_encode(['status' => true, 'data' => $data, 'message' => 'Subcategories List']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Subcategories not available']);
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

        public function customerinfo(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $userData = $this->user->getCustomerData($_POST['user_id']);

                if($userData != NULL){
                    $response = array(
                        "status" => true,
                        "message" => "User info available",
                        "data" => $userData
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

        public function verifyvendorbyotp(){
            if($this->admin->checkIfUserExists($_POST['phone'], "worker")){

                if($this->admin->checkIfUserExists($_POST['phone'], "otp")){//Checking previous records in otp table
                    $this->user->deleteOTP("otp", $_POST['phone']);
                }

                $otp = rand(1000,9999);
                $otpArr = array(
                    "otp" => $otp,
                    "phone" => $_POST['phone']
                );

                $otpData = $this->admin->addData($otpArr, "otp");

                $this->otp($otp,$_POST['phone']);
                $response = array(
                    "status" => true,
                    "message" => "User exists"
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
            echo json_encode($response);
        }

        public function verifyotpvendor(){
            if(isset($_POST['phone']) && isset($_POST['otp'])){
                if($this->user->checkUserOtp($_POST['phone'], $_POST['otp'], 'otp')){
                    $phone = $_POST['phone'];
                    if($this->user->deleteOTP('otp', $phone)){
                        echo json_encode(['status' => true, 'phone'=>$phone, 'message' => "User verified"]);
                    }
                }
                else{
                    echo json_encode(['status' => false, 'message' => "OTP didn't match"]);
                }
            }
            else{
                echo json_encode(['status' => false, 'message' => "Please provide both phone number and OTP"]);
            }
        }

        public function verifycustomerbyotp(){
            if($this->admin->checkIfUserExists($_POST['phone'], "customer")){

                if($this->admin->checkIfUserExists($_POST['phone'], "otp")){//Checking previous records in otp table
                    $this->user->deleteOTP("otp", $_POST['phone']);
                }

                $otp = rand(1000,9999);
                $otpArr = array(
                    "otp" => $otp,
                    "phone" => $_POST['phone']
                );

                $otpData = $this->admin->addData($otpArr, "otp");

                $this->otp($otp,$_POST['phone']);
                $response = array(
                    "status" => true,
                    "message" => "User exists"
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
            echo json_encode($response);
        }

        public function cities(){
            $cities = $this->admin->getAllCities();
            
            if($cities['result']){
                echo json_encode(['status' => true, 'cities'=> $cities['result'], 'message' => "City List"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Cities not available"]);
            }
        }

        public function verifyotpuser(){
            if(isset($_POST['phone']) && isset($_POST['otp'])){
                if($this->user->checkUserOtp($_POST['phone'], $_POST['otp'], 'otp')){
                    $phone = $_POST['phone'];
                    if($this->user->deleteOTP('otp', $phone)){
                        echo json_encode(['status' => true, 'phone'=>$phone, 'message' => "User verified"]);
                    }
                }
                else{
                    echo json_encode(['status' => false, 'message' => "OTP didn't match"]);
                }
            }
            else{
                echo json_encode(['status' => false, 'message' => "Please provide both phone number and OTP"]);
            }
        }

        public function banners(){
            $banners = $this->admin->getActivatedBanners();
            
            if($banners['result']){
                for($i=0; $i < count($banners['result']); $i++){
                    $banners['result'][$i]->banner_image = base_url()."assets/admin/images/banner/".$banners['result'][$i]->banner_image;
                }
                echo json_encode(['status' => true, 'banners'=> $banners['result'], 'message' => "Banners list"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Banners not available"]);
            }
            
        }

        public function terms(){
            $terms = $this->admin->getTerms("terms");
            
            if($terms != NULL){
                echo json_encode(['status' => true, 'paragraph'=> $terms->paragraph, 'message' => "Terms & conditions"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Terms & conditions not available"]);
            }  
        }

        public function privacypolicy(){
            $terms = $this->admin->getTerms("privacy");
            
            if($terms != NULL){
                echo json_encode(['status' => true, 'paragraph'=> $terms->paragraph, 'message' => "Privacy Policy"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Privacy Policy not available"]);
            }  
        }

        public function howitworks(){
            $terms = $this->admin->getTerms("howitworks");
            
            if($terms != NULL){
                echo json_encode(['status' => true, 'paragraph'=> $terms->paragraph, 'message' => "How it works paragraph"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Paragraph not available"]);
            }  
        }

        public function categories(){
            $level1 = $this->admin->getServicesLevelWise("1");
            $level2 = $this->admin->getServicesLevelWise("2");
            $level3 = $this->admin->getServicesLevelWise("3");

            $catArr = array();
            $level1 = $level1['result'];
            $level2 = $level2['result'];
            $level3 = $level3['result'];

            for($i = 0; $i < count($level2); $i++){
                $catArr = array();
                for($j = 0; $j < count($level3); $j++){
                    if($level3[$j]->parent_category == $level2[$i]->id){
                        array_push($catArr, $level3[$j]);
                    }
                }
                $level2[$i]->subcategories = $catArr;
            }

            for($i = 0; $i < count($level1); $i++){
                $catArr = array();
                for($j = 0; $j < count($level2); $j++){
                    if($level2[$j]->parent_category == $level1[$i]->id){
                        array_push($catArr, $level2[$j]);
                    }
                }
                $level1[$i]->subcategories = $catArr;
            }

            if($level1){
                echo json_encode(['status' => true, 'data' => $level1, 'message' => 'Service list']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Services not available']);
            }
        }

        public function userstatus(){

            $user_verified = false;
            $is_kyc_available = false;
            $is_kyc_verified = false;
            $is_bank_details_available = false;
            $is_user_info_available = false;

            $basicDetail = $this->user->getProfileData($_POST['user_id']);
            $is_kyc_available = $this->admin->checkKYCById($_POST['user_id'], 'kyc');
            $is_bank_details_available = $this->admin->checkIfBankDetailsExistById($_POST['user_id'], 'bank_details');
            $is_user_info_available =  $this->admin->isUserInfoAvailable($_POST['user_id']);
            
            if($basicDetail->otp_verified == "1"){
                $user_verified = true;
            }

            if($is_kyc_available){
                $kycStatus = $this->admin->checkIfKYCVerified($_POST['user_id'], 'kyc');
                if($kycStatus->is_verified == "1"){
                    $is_kyc_verified = true;
                }
            }

            $statusArr = array("is_user_verified"=>$user_verified, "is_user_about_available"=>$is_user_info_available, "is_kyc_available"=>$is_kyc_available, "is_kyc_verified"=>$is_kyc_verified, "is_bank_details_available"=>$is_bank_details_available);

            $resp = array("message"=>"User status", "status"=> $statusArr);

            echo json_encode($resp);
        }

        public function contact(){
            $contact = $this->admin->getContact();
            
            if($contact != NULL){
                echo json_encode(['status' => true, 'details'=> $contact, 'message' => "Contact details"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Contact details not available"]);
            }  
        }

        public function awards(){

            if(!isset($_POST['user_id']) || empty($_POST['user_id'])){
                $response = array(
                    "status" => false,
                    "message" => "User ID is mandatory"
                );
            }
            else{
                $awards = $this->admin->getAwards($_POST['user_id']);
                $award_count = $this->admin->getAwardsCount($_POST['user_id']);
                
                if($awards['result']){
                    foreach ($awards['result'] as $key => $value) {
                        $value->file = base_url().'assets/images/documents/'.$value->file;
                    }

                    $response = array(
                        "status" => true,
                        "data" => $awards['result'],
                        "total_count"=> $award_count->total_awards,
                        "message" => "Awards/Certificates available"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "total_count"=> $award_count->total_awards,
                        "message" => "Awards/Certificates not available"
                    );
                }
            }

            echo json_encode($response);
            
        }

    }

    