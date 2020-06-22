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

            $_POST['password'] = $this->admin->crypt($_POST['password'], 'e');

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

        public function verifyadminpassword(){
            $adminData = $this->admin->getAdminProfile($_POST['id']);
            if ($adminData->password === $this->admin->crypt($_POST['password'], 'e')) {
                
                $response = array("message" => "Password is incorrect", "status" => true);
                
            } else {
                $response = array("message" => "Password is incorrect", "status" => false);
            }
            echo json_encode($response);
        }

        public function adminemail(){
            $checkIfAdmin = $this->admin->getAdminByEmail($_POST['email']);
            if($checkIfAdmin){
                $newpass = "admin".rand();
                $data = array("password"=>$this->admin->crypt($newpass, 'e'));
                if($this->user->updateUserIfByEmail('worker', $data, $_POST['email'])){
                    $msg = "Your password has been changed. Use this password to login - ".$newpass;

                    $headers = "From: noreply@troubleshooters.services" . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                    // send email

                    $mail = mail($_POST['email'], "Admin - Troubleshooters", $msg, $headers);

                    if (!$mail) {
                        $response = array(
                            "status" => false,
                            "message" => "Error occurred while updating password, try again"
                        );
                    } else {
                        $response = array(
                            "status" => true,
                            "message" => "Password updated, email sent to the admin"
                        );
                    }
                }
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "This user doesn't exist, not a valid email"
                );
            }
            echo json_encode($response); 
        }

        //APIs
        public function userlogin(){

            $post = json_decode($this->security->xss_clean($this->input->raw_input_stream), true);
            $post['password'] = $this->admin->crypt($post['password'], 'e');

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
                    $user_verified_by_phone = false;
                    $otp_verified = $this->admin->checkIfOTPVerified($data->id, 'worker');
                    if($otp_verified->otp_verified == 1){
                        $user_verified_by_phone = true;
                    }
                    else{
                        $user_verified_by_phone = false;
                    }
                    if($this->admin->checkKYCById($data->id, "kyc")){

                        
                        $is_verified = $this->admin->checkIfKYCVerified($data->id, 'kyc');
                        if($is_verified->is_verified == 1){
                            $success_resp = array(
                                'status' => true,
                                'access_token'=>$jwtToken,
                                'message' => 'Successful Login',
                                'is_kyc_verified' => true,
                                'is_kyc_available' => true,
                                'is_otp_verified' => $user_verified_by_phone
                            );
                        }
                        else{
                            $success_resp = array(
                                'status' => true,
                                'access_token'=>$jwtToken,
                                'message' => 'Successful Login',
                                'is_kyc_verified' => false,
                                'is_kyc_available' => true,
                                'is_otp_verified' => $user_verified_by_phone
                            );
                        }
                    }
                    else{
                        $success_resp = array(
                            'status' => true,
                            'access_token'=>$jwtToken,
                            'message' => 'Successful Login',
                            'is_kyc_verified' => false,
                            'is_kyc_available' => false,
                            'is_otp_verified' => $user_verified_by_phone
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
                    if($data->sub_profession != ""){
                        $serviceInRequest = $data->sub_profession;
                        if($serviceInRequest != ""){
                            $tempServices = explode(",", $serviceInRequest);
                            $serviceArr = [];
                            foreach($tempServices as $ser){
                                $serviceArr[] = $this->admin->getServiceById($ser);
                            }
                            $data->sub_profession = $serviceArr;
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
         
                foreach($data as $request){
                    $serviceInRequest = $request->services;
                    if($serviceInRequest != ""){
                        $tempServices = explode(",", $serviceInRequest);
                        $serviceArr = [];
                        foreach($tempServices as $ser){
                            $serviceArr[] = $this->admin->getServiceById($ser);
                        }
                        $request->services = $serviceArr;
                    }
                }
    
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
                    foreach($data as $request){
                        $serviceInRequest = $request->services;
                        if($serviceInRequest != ""){
                            $tempServices = explode(",", $serviceInRequest);
                            $serviceArr = [];
                            foreach($tempServices as $ser){
                                $serviceArr[] = $this->admin->getServiceById($ser);
                            }
                            $request->services = $serviceArr;
                        }
                    }
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
                    foreach($data as $request){
                        $serviceInRequest = $request->services;
                        if($serviceInRequest != ""){
                            $tempServices = explode(",", $serviceInRequest);
                            $serviceArr = [];
                            foreach($tempServices as $ser){
                                $serviceArr[] = $this->admin->getServiceById($ser);
                            }
                            $request->services = $serviceArr;
                        }
                    }
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

        public function bookingcomments(){
            $data = $this->admin->getCommentsByBookingId($_POST['booking_id']);

            if($data){
                $response = array(
                    "status"=>true,
                    "data"=>$data,
                    "message"=>"Comment available"
                );
            }
            else{
                $response = array(
                    "status"=>false,
                    "message"=>"Comments not available"
                );
            }
            echo json_encode($response);
        }


        public function kycdoctype(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->checkKYCById($_POST['user_id'], "kyc");
                
                if($data){
                    $is_verified = $this->admin->checkIfKYCVerified($_POST['user_id'], 'kyc');
                    $is_kyc_verified = false;
                    if($is_verified->is_verified == 1){
                        $is_kyc_verified = true;
                    }

                    $response = array(
                        "status" => true,
                        "message" => "KYC Details",
                        "is_kyc_verified" => $is_kyc_verified,
                        "document" => $is_verified->id_type
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "KYC not available"
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

        public function waitingduration(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getBookingInfo($_POST['user_id'], $_POST['booking_id']);
                if($data){
                    $hasReached = false;
                    $hasCrossed48Mins = false;
                    if($data[0]->reached_location_at == ""){
                        $hasReached = false;
                    }
                    else{
                        $timeEnd = new DateTime('now');
                        $timeStart = new DateTime('@'.$data[0]->reached_location_at);
                        $interval = $timeStart->diff($timeEnd);
                        $interval = $interval->format('%H,%i');
                        $intervalArr = explode(",",$interval);
                        $totalTimeInMinutes = ((int) $intervalArr[0] * 60) + ((int) $intervalArr[1]);

                        if($totalTimeInMinutes >= 15){
                            $hasReached = true;
                        }

                        if($totalTimeInMinutes >= 48){
                            $hasCrossed48Mins = true;
                        }
                    }
                    $response = array(
                        "status"=>true,
                        "message"=> "Vendor waiting duration",
                        "hasCrossedTimeLimit" => $hasReached,
                        "hasCrossed48Mins" => $hasCrossed48Mins
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Booking not available"
                    );
                }
                echo json_encode($response);
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
            //echo json_encode($response);
        }

        //Customer APIs

        public function verifyotpcustomer(){
            
            if($this->user->checkUserOtp($_POST['phone'], $_POST['otp'], 'otp')){
                $POST['otp_verified'] = 1;
                $phone = $_POST['phone'];
                unset($_POST['phone']);
                $type = $_POST['page'];
                unset($_POST['page']);
                if($this->user->updateUserIfVerified('customer', $POST, $phone)){
                    if($this->user->deleteOTP('otp', $phone)){

                        if($type == "signup"){
                            echo json_encode(['status'=> true, 'message' => "User verified and registered successfully"]);
                        }
                        else if($type == "changepassword"){
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
            }
            else{
                echo json_encode(['status' => false, 'message' => "OTP didn't match"]);
            }
        }

        public function customerlogin(){

            //$_POST = json_decode($this->security->xss_clean($this->input->raw_input_stream), true);
            $_POST['password'] = $this->admin->crypt($_POST['password'], 'e');

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

        //Get all services including subcategories
        public function allservices(){
            $data = $this->admin->getAllServices();

            if($data){
                echo json_encode(['status' => true, 'data' => $data['result'], 'message' => 'Service list']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Services not available']);
            }
        }

        public function customerbookingstatus(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getBookingInfoByCustomerID($_POST['user_id'], $_POST['booking_id']);
                if($data){
                    foreach($data as $request){
                        $serviceInRequest = $request->services;
                        if($serviceInRequest != ""){
                            $tempServices = explode(",", $serviceInRequest);
                            $serviceArr = [];
                            foreach($tempServices as $ser){
                                $serviceArr[] = $this->admin->getServiceById($ser);
                            }
                            $request->services = $serviceArr;
                        }
                    }
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
    
        public function customerbookinginfo(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getBookingInfoByCustomerID($_POST['user_id'], $_POST['booking_id']);
                if($data){
                    foreach($data as $request){
                        $serviceInRequest = $request->services;
                        if($serviceInRequest != ""){
                            $tempServices = explode(",", $serviceInRequest);
                            $serviceArr = [];
                            foreach($tempServices as $ser){
                                $serviceArr[] = $this->admin->getServiceById($ser);
                            }
                            $request->services = $serviceArr;
                        }
                    }
                    $services = $this->admin->getServicesFromBooking($_POST['booking_id']);
                    $started_at = $services[0]->started_at;
                    $completed_at = $services[0]->completed_at;
                    $services = $services[0]->services;
        
                    $allServices = explode(",",$services);
                    $timeEnd = new DateTime('@'.$completed_at);
                    $timeStart = new DateTime('@'.$started_at);
                    $interval = $timeStart->diff($timeEnd);
                    $interval = $interval->format('%H,%i');
                    $intervalArr = explode(",",$interval);
                    $totalTimeInMinutes = ((int) $intervalArr[0] * 60) + ((int) $intervalArr[1]);
                    $totalBill = 0;
                    $billWithMaterialCharge = 0; //including material charges
                    
                   
                    $totalBill = (float) $data[0]->amount;
                        

                    $data[0]->service_charges = $totalBill;
                    $material_procurement_charges = 0;
                    if($data[0]->service_charge_added == "1"){
                        $added_amount_json = json_decode($data[0]->bill_amount, true);

                        foreach($added_amount_json as $key=>$value){
                            $added_amount += $value;
                        }
                        
                        if($added_amount > 0){
                            $material_procurement_charges =  (float) $added_amount * 0.18;
                        }
                    }
                    $billWithMaterialCharge = $material_procurement_charges + $totalBill;
                    $data[0]->material_procurement_charges = $material_procurement_charges;
                    $gst = $billWithMaterialCharge * 0.18;
                    $data[0]->gst = $gst;
                    $data[0]->billWithMaterialCharge = $billWithMaterialCharge;
                    $data[0]->finalBill = $billWithMaterialCharge + $gst;
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

        public function services(){
            $data = $this->admin->getAllParentServices();

            if($data){
                echo json_encode(['status' => true, 'data' => $data['result'], 'message' => 'Service list']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Services not available']);
            }
        }

        public function services2(){
            $data = $this->admin->getAllParentServicesWithLimit(6);
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

        function getParentService($service){
            $serviceData = $this->admin->getServiceById($service);
            if($serviceData->level == "1"){
                return $serviceData;
            }
            return $this->getParentService($serviceData->parent_category);
        }

        public function nearbyvendor(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $servicesRequested = $_POST['profession'];
                $saggregatedArr = [];
                if($servicesRequested != ""){
                    $services = explode(",", $servicesRequested);
                    $prevService;
                    $serviceToCompare;
                    $parsedElements = [];
    
                    for($i = 0; $i < count($services); $i++){
                        if(in_array($services[$i], $parsedElements)){
                            continue;
                        }
                        $prevService = $this->getParentService($services[$i]);
                        $tempArr = [];
                        array_push($tempArr, $services[$i]);
                        for($j = $i+1; $j < count($services); $j++){
                            $serviceToCompare = $this->getParentService($services[$j]);
                            if($prevService->id === $serviceToCompare->id){
                                array_push($tempArr, $services[$j]);
                                array_push($parsedElements, $services[$j]);
                            }
                        }
                        array_push($saggregatedArr, $tempArr);
                    }
                }
                $flag = false;
                $commaSeparatedServices = "";
                $responseMessage = "";
                foreach($saggregatedArr as $serviceGroup){
                    $commaSeparatedServices = "";
                    foreach($serviceGroup as $commaservice){
                        $commaSeparatedServices .= $commaservice.",";
                    }
                    $commaSeparatedServices = rtrim($commaSeparatedServices, ",");

                    $allVendors = $this->admin->getAllVerifiedWorkers($commaSeparatedServices);
                
                    
                    $reqNo = rand(10000,99999);
                    $distanceArr = array();
                    $vendorArr = array();
                    foreach($allVendors as $key => $value){
                        $dist = $this->distance((float) $_POST['lat'], (float) $_POST['lng'], (float) $value->lat, (float) $value->lng, "K");
                        if($dist < 10){
                            array_push($distanceArr, $dist);
                            array_push($vendorArr, $value->id);
                        }
                    }
                    
                    if(!empty($distanceArr)){
                        $minDistance = min($distanceArr);
                        $indexOfMinDistance = array_search($minDistance, $distanceArr);
                        $vendor_id = $vendorArr[$indexOfMinDistance];
                        $singleService = explode(",", $commaSeparatedServices);
                        $singleService = $singleService[0];
                        
                        $parentService = $this->getParentService($singleService);
                        $parentService = $parentService->service_name;
                    
                        $post = array();
                        $post['vendor_id'] = $vendor_id;
                        $post['req_no'] = $reqNo."".$_POST['user_id'];
                        $post['request_status'] = 0;
                        $post['customer_id'] = $_POST['user_id'];
                        $post['lat'] = $_POST['lat'];
                        $post['lng'] = $_POST['lng'];
                        $post['quantity'] = $_POST['quantity'];
                        $post['services'] = $commaSeparatedServices;
                        $post['last_assigned_to'] = $vendor_id;
                        date_default_timezone_set('Asia/Kolkata');
                        $post['vendor_changed_at'] = time();
                        $post['created_at'] = time();
                        $this->admin->addData($post, 'request');
                        $vendor = $this->user->getProfileData($vendor_id);
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
                        $responseMessage .= "Your request has been sent to nearby ".$parentService." Service. "; 
                    }
                    else{
                        $responseMessage .= "Nearby vendors are not available for the other services you chose. ";
                    }
                    
                }

                if($flag){
                    $response = array(
                        "status" => true,
                        "message" => $responseMessage
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
                    foreach($data as $request){
                        $serviceInRequest = $request->services;
                        if($serviceInRequest != ""){
                            $tempServices = explode(",", $serviceInRequest);
                            $serviceArr = [];
                            foreach($tempServices as $ser){
                                $serviceArr[] = $this->admin->getServiceById($ser);
                            }
                            $request->services = $serviceArr;
                        }
                    }
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

        public function completeduserbookings(){

            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->getAllCompletedBookings($_POST['user_id']);
                if($data){
                    foreach($data as $request){
                        $serviceInRequest = $request->services;
                        if($serviceInRequest != ""){
                            $tempServices = explode(",", $serviceInRequest);
                            $serviceArr = [];
                            foreach($tempServices as $ser){
                                $serviceArr[] = $this->admin->getServiceById($ser);
                            }
                            $request->services = $serviceArr;
                        }
                    }
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
                    if($locData->distance < 10){
                        date_default_timezone_set('Asia/Kolkata');
                        $reachArr['reached_location_at'] = time();
                        $this->admin->updateVendorReachTime($reachArr, $_POST['booking_id']);
                    }
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
                $referal = $this->admin->getReferralAmount();
                $userData = $this->user->getCustomerData($_POST['user_id']);

                if($userData != NULL){
                    $response = array(
                        "status" => true,
                        "message" => "User info available",
                        "data" => $userData,
                        'refer_amount'=> $referal->amount
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

        public function vehicles(){
            $cities = $this->admin->getAllVehicles();
            
            if($cities['result']){
                echo json_encode(['status' => true, 'vehicles'=> $cities['result'], 'message' => "Vehicles List"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Vehicles not available"]);
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

        public function declaration(){
            $terms = $this->admin->getTerms("declaration");
            
            if($terms != NULL){
                echo json_encode(['status' => true, 'paragraph'=> $terms->paragraph, 'message' => "Declaration paragraph"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Declaration not available"]);
            }  
        }

        public function packages(){
            $packages = $this->admin->getAllPackages();
            $memberships = $this->admin->getAllMemberships();

            $data = array();

            $data2 = array();
        
            $timeNow = date_create(date("Y-m-d"));
            foreach($packages as $package){
                $timeEnd = date_create($package->to_date);
                $diff = date_diff($timeNow, $timeEnd);
                $interval = $diff->format("%R%a");
                if($interval > 0){
                    $package->image = base_url().'assets/admin/images/'.$package->image;
                    $data[] = $package;
                }
            }

            foreach($memberships as $membership){
                $timeEnd = date_create($membership->to_date);
                $diff = date_diff($timeEnd, $timeNow);
                $interval = $diff->format("%R%a");
                if($interval < 0){
                    $membership->image = base_url().'assets/admin/images/'.$membership->image;
                    $data2[] = $membership;
                }
            }
            $serviceArr = [];
            $serviceArr2 = [];
            foreach($data as $pack){
                $serviceArr = [];
                $services = json_decode($pack->services, false);
                foreach($services as $service){
                    $serviceArr[] = $this->admin->getServiceById($service->service);
                }
                $pack->service_detail = $serviceArr;
                $pack->type = "package";
            }

            foreach($data2 as $mem){
                $serviceArr2 = [];
                $services = json_decode($mem->services, false);
                foreach($services as $service){
                    $serviceArr2[] = $this->admin->getServiceById($service->service);
                }
                $mem->service_detail = $serviceArr2;
                $mem->type = "membership";
            }
            $finalArr = array_merge($data, $data2);
          
            if(count($finalArr) > 0){
                $response = array("status"=>true, "message"=>"Packages & memberships available", "data"=>$finalArr);
            }
            else{
                $response = array("status"=>false, "message"=>"Packages & memberships not available");
            }

            echo json_encode($response); 
        }

        public function checkIfServicesBought(){
            if($_POST['type']=="package"){
                if($this->admin->checkIfUserBoughtPackage($_POST['package_id'], $_POST['user_id']) > 0){
                    $response = array(
                        "status" => true,
                        "is_bought"=> true,
                        "message" => "You have already bought this package"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "is_bought"=> false,
                        "message" => "Not Bought"
                    );
                }
            }
            else if($_POST['type']=="membership"){
                if($this->admin->checkIfUserBoughtMembership($_POST['membership_id'], $_POST['user_id']) > 0){
                    $response = array(
                        "status" => true,
                        "is_bought"=> true,
                        "message" => "You have already bought this membership"
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "is_bought"=> false,
                        "message" => "Not Bought"
                    );
                }
            }

            echo json_encode($response);
        }

        public function memberships(){
            $packages = $this->admin->getAllMemberships();

            $data = array();
        
            $timeNow = date_create(date("Y-m-d"));

            foreach($packages as $package){
                $timeEnd = date_create($package->to_date);
                $diff = date_diff($timeEnd, $timeNow);
                $interval = $diff->format("%R%a");
                if($interval < 0){
                    $package->image = base_url().'assets/admin/images/'.$package->image;
                    $data[] = $package;
                }
            }
            $serviceArr = [];
            foreach($data as $pack){
                $serviceArr = [];
                $services = json_decode($pack->services, false);
                foreach($services as $service){
                    $serviceArr[] = $this->admin->getServiceById($service->service);
                }
                $pack->service_detail = $serviceArr;
                $pack->type = "membership";
            }
          
            if(count($data) > 0){
                $response = array("status"=>true, "message"=>"Memberships available", "data"=>$data);
            }
            else{
                $response = array("status"=>false, "message"=>"Membership not available");
            }

            echo json_encode($response); 
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

            if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
                $user_verified = false;
                $is_kyc_available = false;
                $is_kyc_verified = false;
                $is_bank_details_available = false;
                $is_user_info_available = false;
                $is_personal_detail_completed = false;
                $is_training_completed = false;
                $is_document_uploaded = false;
                $is_declaration_completed = false;
                $is_current_address_completed = false;
                $document_image = "";
                $parent_name = "";
                $declaration = "";
                $gender = ""; $dob = ""; $p_street=""; $p_pincode = ""; $p_city=""; $p_state="";
                $face_photo = ""; $side_face_photo=""; $full_body_photo = ""; $tool_photo=""; $mode_of_transport="";

                $c_street = ""; $c_pincode = ""; $c_city=""; $c_state="";

                $basicDetail = $this->user->getProfileData($_POST['user_id']);
                $is_kyc_available = $this->admin->checkKYCById($_POST['user_id'], 'kyc');
                $is_bank_details_available = $this->admin->checkIfBankDetailsExistById($_POST['user_id'], 'bank_details');
                $is_user_info_available =  $this->admin->isUserInfoAvailable($_POST['user_id']);
                $is_user_award_available = $this->admin->isAwardAvailable($_POST['user_id']);
                $last_seen_video = $this->user->getLastSeenVideo($_POST['user_id']);

                if($is_kyc_available){
                    $document_image = $this->admin->getKycByID($_POST['user_id'])[0]->img_front_side;
                    $parent_name = $this->admin->getKycByID($_POST['user_id'])[0]->parent_name;
                    $gender = $this->admin->getKycByID($_POST['user_id'])[0]->gender;
                    $dob = $this->admin->getKycByID($_POST['user_id'])[0]->dob;
                    $p_street = $this->admin->getKycByID($_POST['user_id'])[0]->p_street;
                    $p_pincode = $this->admin->getKycByID($_POST['user_id'])[0]->p_pincode;
                    $p_city = $this->admin->getKycByID($_POST['user_id'])[0]->p_city;
                    $p_state = $this->admin->getKycByID($_POST['user_id'])[0]->p_state;
                    $declaration = $this->admin->getKycByID($_POST['user_id'])[0]->declaration;

                    $c_street = $this->admin->getKycByID($_POST['user_id'])[0]->c_street;
                    $c_pincode = $this->admin->getKycByID($_POST['user_id'])[0]->c_pincode;
                    $c_city = $this->admin->getKycByID($_POST['user_id'])[0]->c_city;
                    $c_state = $this->admin->getKycByID($_POST['user_id'])[0]->c_state;
                }

                if($document_image != ""){
                    $is_document_uploaded = true;
                }

                if((int)$last_seen_video->training_video_no == 7){
                    $is_training_completed = true;
                }
                
                if($basicDetail != NULL && $basicDetail->otp_verified == "1"){
                    $user_verified = true;
                }

                if($basicDetail != NULL){
                    $face_photo = $basicDetail->face_photo;
                    $side_face_photo = $basicDetail->side_face_photo;
                    $full_body_photo = $basicDetail->full_body_photo;
                    $tool_photo = $basicDetail->tool_photo;
                    $mode_of_transport = $basicDetail->mode_of_transport;
                }

                //Personal Detail check
                if($parent_name != "" && $gender != "" && $dob != "" && $p_street != "" && $p_pincode != "" && $p_city != "" && $p_state != "" && $face_photo != "" && $side_face_photo != "" && $full_body_photo != "" && $tool_photo != "" && $mode_of_transport != ""){
                    $is_personal_detail_completed = true;
                }

                //Declaration
                if($declaration != ""){
                    $is_declaration_completed = true;
                }

                //Current City
                if($c_city != "" && $c_street != "" && $c_pincode != "" && $p_state != ""){
                    $is_current_address_completed = true;
                }

                if($is_kyc_available){
                    $kycStatus = $this->admin->checkIfKYCVerified($_POST['user_id'], 'kyc');
                    if($kycStatus->is_verified == "1"){
                        $is_kyc_verified = true;
                    }
                }

                $statusArr = array("is_user_verified"=>$user_verified, "is_rating_available"=>false, "is_user_award_available"=>$is_user_award_available, "is_user_about_available"=>$is_user_info_available, "is_kyc_available"=>$is_kyc_available, "is_kyc_verified"=>$is_kyc_verified, "is_bank_details_available"=>$is_bank_details_available, "is_training_completed" => $is_training_completed, "is_document_uploaded"=>$is_document_uploaded, "is_personal_detail_completed"=>$is_personal_detail_completed, "is_declaration_completed"=>$is_declaration_completed, "is_current_address_completed"=> $is_current_address_completed);

                $resp = array("message"=>"User status", "status"=> $statusArr);
            }
            else{
                $resp = array("message"=>"User ID is mandatory", "status"=> false);
            }

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

        public function trainingvideos(){
            $training = $this->admin->getAllTrainingVideos();
            
            if($training['result'] != NULL){
                foreach ($training['result'] as $key => $value) {
                    $value->video_file = base_url().'assets/admin/videos/'.$value->video_file;
                    if($value->video_thumb != ""){
                        $value->video_thumb = base_url().'assets/admin/images/video_thumb/'.$value->video_thumb;
                    } 
                }
                echo json_encode(['status' => true, 'details'=> $training['result'], 'message' => "Training videos available"]);
            }
            else{
                echo json_encode(['status' => false, 'message' => "Training videos not available"]);
            } 
        }

        public function lastseentrainingvideo(){
            
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $last_seen_video = $this->user->getLastSeenVideo($_POST['user_id']);
                if((int)$last_seen_video->training_video_no == 0){
                    $response = array(
                        "status" => false,
                        "message" => "0 tutorial videos seen by the user"
                    );
                }
                else{
                    $response = array(
                        "status" => true,
                        "message" => "Tutorials seen by the user",
                        "last_seen_tutorial" => $last_seen_video->training_video_no
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
                        $value->file = base_url().'assets/admin/images/documents/'.$value->file;
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

        public function rating(){

            $data = $this->admin->getVendorRating($_POST['user_id']);
            if($data->service_quality_rating == null){
                $data->service_quality_rating = "0";
            }

            if($data->behaviour_rating == null){
                $data->behaviour_rating = "0";
            }

            if($data->speed_of_work_rating == null){
                $data->speed_of_work_rating = "0";
            }

            $data->total_average_rating = ((float)$data->service_quality_rating + (float)$data->behaviour_rating + (float)$data->speed_of_work_rating)/3;
            $data->total_average_rating = (string)$data->total_average_rating;

            $response = array(
                "status" => true,
                "message" => "Vendor rating",
                "rating_data" => $data
            );
            
            echo json_encode($response);
        }

        public function notification(){
            $data = $this->admin->getVendorNotification($_POST['user_id']);

            if($data['result']){
                $response = array(
                    "status" => true,
                    "message" => "Vendor notifications available",
                    "notification" => $data['result']
                );
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Vendor notifications not available"
                );
            }
            echo json_encode($response);
        }

        public function faq(){
            $faqTitles = $this->admin->getAllFAQTitle();
            $allFaqs = $this->admin->getAllFAQs();

            $faqData = array("titles"=> $faqTitles['result'], "faqList"=> $allFaqs);

            echo json_encode($faqData);
        }

        public function homepage(){
            $homeData = $this->admin->getHomePage();

            $homeData = array("data"=> $homeData, "status"=>true);

            echo json_encode($homeData);
        }

        public function vendortokenupdate(){
            if(!isset($_POST['user_id']) || empty($_POST['user_id'])){
                $response = array(
                    "status" => false,
                    "message" => "User ID is mandatory"
                );
            }
            else{
                if($this->admin->checkUserById($_POST['user_id'], 'worker')){
                    $response = array(
                        "status" => true,
                        "message" => "User Exists"
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

        public function bookingtimeline(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            
            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){
                $data = $this->admin->bookingtimeline($_POST['booking_id']);
                if($data){
                    
                    $serviceInRequest = $data[0]->services;
                    if($serviceInRequest != ""){
                        $tempServices = explode(",", $serviceInRequest);
                        $serviceArr = [];
                        foreach($tempServices as $ser){
                            $serviceArr[] = $this->admin->getServiceById($ser);
                        }
                        $data[0]->services = $serviceArr;
                    }
                    
                    $response = array(
                        "status" => true,
                        "message" => "Booking timeline",
                        "location" => $data
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Activities not available"
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

        //Crons
        public function check45mins(){
            $bookingsWithStatus1 = $this->admin->getAllBookingWithStatus1();
            foreach($bookingsWithStatus1 as $book){
                
                $timeEnd = new DateTime('now');
                $timeStart = new DateTime('@'.$book->created_at);
                $interval = $timeStart->diff($timeEnd);
                $interval = $interval->format('%H,%i');
                $intervalArr = explode(",",$interval);
                $totalTimeInMinutes = ((int) $intervalArr[0] * 60) + ((int) $intervalArr[1]);

                $vendor = $this->user->getProfileData($book->vendor_id);
                $to = $vendor->device_id;

                if($totalTimeInMinutes > 45){
                    $data['booking_status'] = 4;
                    if($this->admin->updateBookingStatus($data, $book->booking_id)){
                        $vendor = $this->user->getProfileData($book->vendor_id);
                        $notificationMessage = array();
                        $notificationMessage = array(
                            "title" => "Troubleshooter",
                            "body" => "Booking (Booking ID: ".$book->booking_id.")! has been cancelled!"
                        );
                        $bodyData = array(
                            'action'=> "booking_cancelled"
                        );
                        $api_key = "AAAA0W6cR-g:APA91bGr4S_9LPdoWVc9k3aY5_6Nh3e_orRbsj6dLOq59nAC5GmLS9-21Au2figrAoCu9VjrsgWsd3taKiPvj2s2-niwWGDGA0B5KGGjFdCCZMQMcKdelOcexyXyuNcmcm_iRW9qGEJr";
                        $to = $vendor->device_id;
                        $this->send_notification($api_key, $to, $notificationMessage, $bodyData);
                    }
                }
            }
        }

        public function check20mins(){
            $bookingsWithStatus1 = $this->admin->getAllBookingAfterReachingLocation();
            foreach($bookingsWithStatus1 as $book){
                
                $timeEnd = new DateTime('now');
                $timeStart = new DateTime('@'.$book->reached_location_at);
                $interval = $timeStart->diff($timeEnd);
                $interval = $interval->format('%H,%i');
                $intervalArr = explode(",",$interval);
                $totalTimeInMinutes = ((int) $intervalArr[0] * 60) + ((int) $intervalArr[1]);

                $vendor = $this->user->getProfileData($book->vendor_id);
                $to = $vendor->device_id;

                if($totalTimeInMinutes > 20){
                    $data['booking_status'] = 4;
                    if($this->admin->updateBookingStatus($data, $book->booking_id)){
                        $vendor = $this->user->getProfileData($book->vendor_id);
                        $notificationMessage = array();
                        $notificationMessage = array(
                            "title" => "Troubleshooter",
                            "body" => "Booking (Booking ID: ".$book->booking_id.")! has been cancelled due to customer inavailablity!"
                        );
                        $bodyData = array(
                            'action'=> "booking_cancelled"
                        );
                        $api_key = "AAAA0W6cR-g:APA91bGr4S_9LPdoWVc9k3aY5_6Nh3e_orRbsj6dLOq59nAC5GmLS9-21Au2figrAoCu9VjrsgWsd3taKiPvj2s2-niwWGDGA0B5KGGjFdCCZMQMcKdelOcexyXyuNcmcm_iRW9qGEJr";
                        $to = $vendor->device_id;
                        $this->send_notification($api_key, $to, $notificationMessage, $bodyData);
                    }
                }
            }
        }

        public function fiveMinsLeadChange(){
            $requests = $this->admin->getAllRequestWithStatus0();
            foreach($requests as $request){
                $timeEnd = new DateTime('now');
                $timeStart = new DateTime('@'.$request->vendor_changed_at);
                $interval = $timeStart->diff($timeEnd);
                $interval = $interval->format('%H,%i');
                $intervalArr = explode(",",$interval);
                $totalTimeInMinutes = ((int) $intervalArr[0] * 60) + ((int) $intervalArr[1]);
                
                if($totalTimeInMinutes > 5){
                    
                    $vendorProfile = $this->user->getProfileData($request->vendor_id);
                    $allVendors = $this->admin->getAllVerifiedVendorsExceptAssigned($vendorProfile->primary_profession, $request->last_assigned_to);
                    $distanceArr = array();
                    $vendorArr = array();
                    foreach($allVendors as $key => $value){
                        $dist = $this->distance((float) $request->lat, (float) $request->lng, (float) $value->lat, (float) $value->lng, "K");
                        
                        if($dist < 10){
                            array_push($distanceArr, $dist);
                            array_push($vendorArr, $value->id);
                            //$nearest_vendors[] = $value;
                        }
                    }

                    if(!empty($distanceArr)){
                        $minDistance = min($distanceArr);
                        $indexOfMinDistance = array_search($minDistance, $distanceArr);
                        $vendor_id = $vendorArr[$indexOfMinDistance];
                        $post = array();
                        $post['vendor_id'] = $vendor_id;
                        $post['last_assigned_to'] = $request->last_assigned_to.','.$vendor_id;
                        date_default_timezone_set('Asia/Kolkata');
                        $post['vendor_changed_at'] = time();
                        if($this->admin->updatebookingrequest('request', $post, $request->id, $request->req_no)){
                            $vendor = $this->user->getProfileData($vendor_id);
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
                        }
                    }
                    else{
                        $post = array();
                        $post['request_status'] = 2;
                        $this->admin->updatebookingrequest('request', $post, $request->id, $request->req_no);
                    }
                }
            }
            
        }

    }

    