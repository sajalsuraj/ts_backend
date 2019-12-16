<?php

    require APPPATH . 'libraries/ImplementJWT.php';
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Add extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->objOfJwt = new ImplementJwt();
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: access_token, Cache-Control');
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

        public function signup(){

            $post = json_decode($this->security->xss_clean($this->input->raw_input_stream));
            if(empty($post->phone)){
                $response = array(
                    "status" => false,
                    "message" => "Phone number missing"
                );
            }
            else{
                if($this->admin->checkIfUserExists($post->phone, 'worker')){
                    $response = array(
                        "status" => false,
                        "message" => "User already exists!"
                    );
                }
                else{
                    if(!isset($post->lat) && !isset($post->lng)){
                        $response = array(
                            "status" => false,
                            "message" => "User location(Lat, Lng) is missing"
                        );
                    }
                    else{
                        if(empty($post->lat) && empty($post->lng)){
                            $response = array(
                                "status" => false,
                                "message" => "User location(Lat, Lng) should not be empty"
                            );
                        }
                        else if(empty($post->lat) || empty($post->lng)){
                            $response = array(
                                "status" => false,
                                "message" => "User location(Lat, Lng) should not be empty, Both latitude and longitude mandatory"
                            );
                        }
                        else{
                            $post->password = md5($post->password);
                            $post->type = "worker";
                            $data = $this->admin->addData($post, "worker");
                        
                            if($data){

                                $otp_status = $this->admin->getUserByPhone($post->phone, 'worker');
                                $response = array(
                                    "status" => true,
                                    "is_otp_verified" => boolval($otp_status->otp_verified),
                                    "phone" => $post->phone,
                                    "message" => "Successfully added"
                                );

                                $otp = rand(1000,9999);
                                $otpArr = array(
                                    "otp" => $otp,
                                    "phone" => $post->phone
                                );

                                $otpData = $this->admin->addData($otpArr, "otp");

                                $this->otp($otp,$post->phone);

                            }
                            else{
                                $response = array(
                                    "status" => false,
                                    "message" => "Error occurred while adding"
                                );
                            }
                        }
                    }
                    
                } 
            }
            echo json_encode($response);
            
        }

        public function customersignup(){

            //$post = json_decode($this->security->xss_clean($this->input->raw_input_stream));
            if(empty($_POST['phone'])){
                $response = array(
                    "status" => false,
                    "message" => "Phone number missing"
                );
            }
            else{
                if($this->admin->checkIfUserExists($_POST['phone'], 'customer')){
                    $response = array(
                        "status" => false,
                        "message" => "User already exists!"
                    );
                }
                else{
                    $_POST['password'] = md5($_POST['password']);
                    $data = $this->admin->addData($_POST, "customer");
                
                    if($data){

                        $otp_status = $this->admin->getUserByPhone($_POST['phone'], 'customer');
                        $response = array(
                            "status" => true,
                            "is_otp_verified" => boolval($otp_status->otp_verified),
                            "phone" => $_POST['phone'],
                            "message" => "Successfully added"
                        );

                        $otp = rand(1000,9999);
                        $otpArr = array(
                            "otp" => $otp,
                            "phone" => $_POST['phone']
                        );

                        $otpData = $this->admin->addData($otpArr, "otp");

                        $this->otp($otp,$_POST['phone']);

                    }
                    else{
                        $response = array(
                            "status" => false,
                            "message" => "Error occurred while adding"
                        );
                    }
                } 
            }
            echo json_encode($response);
            
        }

        public function kycdetail(){
            $count = 0;
            
            if(isset($_POST['id_type'])){
                if($_POST['id_type'] == "PAN"){
                    if(isset($_FILES["img_front_side"])){
                        $folder= './assets/admin/images/documents/';
                        $temp = explode(".", $_FILES["img_front_side"]["name"]);
                        $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                        $_POST['img_front_side'] = round(microtime(true)).'front.'.$temp[1];
                        move_uploaded_file($_FILES["img_front_side"]["tmp_name"], $target_file_img); 
                    }
                }
                else{
                    if(isset($_FILES["img_front_side"])){
                        $folder= './assets/admin/images/documents/';
                        $temp = explode(".", $_FILES["img_front_side"]["name"]);
                        $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                        $_POST['img_front_side'] = round(microtime(true)).'front.'.$temp[1];
                        move_uploaded_file($_FILES["img_front_side"]["tmp_name"], $target_file_img);
                    }
        
                    if(isset($_FILES["img_back_side"])){
                        $folder= './assets/admin/images/documents/';
                        $temp = explode(".", $_FILES["img_back_side"]["name"]);
                        $target_file_img = $folder. round(microtime(true)).'back.'.$temp[1]; 
                        $_POST['img_back_side'] = round(microtime(true)).'back.'.$temp[1];
                        move_uploaded_file($_FILES["img_back_side"]["tmp_name"], $target_file_img); 
                    }
                }
            }

            if(!$this->admin->checkKYCById($_POST['user_id'], 'kyc')){
                $count++;
                $_POST['steps_filled'] = $count;
                if($this->admin->addData($_POST, 'kyc')){
                    $response = array(
                        "status" => true,
                        "message" => "KYC Successfully updated, you will be live once your profile gets verified",
                        "steps_filled" => $count
                    );
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Error occurred while updating"
                    );
                }
            }
            else{
                $steps_filled = $this->admin->checkKYCStepsById($_POST['user_id'], 'kyc');
                
                $count = (int) $steps_filled->steps_filled;
                $count++;
                $_POST['steps_filled'] = $count;
                if($this->user->kycupdate('kyc', $_POST, $_POST['user_id'])){
                    if($count < 4){
                        $response = array(
                            "status" => true,
                            "message" => "KYC Successfully updated, Please complete the form in next processes",
                            "steps_filled" => $count
                        );
                    }
                    else{
                        $response = array(
                            "status" => true,
                            "message" => "KYC Successfully completed, you will be live once your profile gets verified",
                            "steps_filled" => $count
                        );
                    }
                    
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Error occurred while updating"
                    );
                }
            } 
            echo json_encode($response);

        }

        public function userkyc(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);
            $count = 0;

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){

                if(isset($_POST['id_type'])){
                    if($_POST['id_type'] == "PAN"){
                        if(isset($_FILES["img_front_side"])){
                            $folder= './assets/admin/images/documents/';
                            $temp = explode(".", $_FILES["img_front_side"]["name"]);
                            $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                            $_POST['img_front_side'] = round(microtime(true)).'front.'.$temp[1];
                            move_uploaded_file($_FILES["img_front_side"]["tmp_name"], $target_file_img); 
                        }
                    }
                    else{
                        if(isset($_FILES["img_front_side"])){
                            $folder= './assets/admin/images/documents/';
                            $temp = explode(".", $_FILES["img_front_side"]["name"]);
                            $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                            $_POST['img_front_side'] = round(microtime(true)).'front.'.$temp[1];
                            move_uploaded_file($_FILES["img_front_side"]["tmp_name"], $target_file_img);
                        }
            
                        if(isset($_FILES["img_back_side"])){
                            $folder= './assets/admin/images/documents/';
                            $temp = explode(".", $_FILES["img_back_side"]["name"]);
                            $target_file_img = $folder. round(microtime(true)).'back.'.$temp[1]; 
                            $_POST['img_back_side'] = round(microtime(true)).'back.'.$temp[1];
                            move_uploaded_file($_FILES["img_back_side"]["tmp_name"], $target_file_img); 
                        }
                    }
                }

                if(!$this->admin->checkKYCById($_POST['user_id'], 'kyc')){
                    $count++;
                    $_POST['steps_filled'] = $count;
                    if($this->admin->addData($_POST, 'kyc')){
                        $response = array(
                            "status" => true,
                            "message" => "KYC Successfully updated, you will be live once your profile gets verified",
                            "steps_filled" => $count
                        );
                    }
                    else{
                        $response = array(
                            "status" => false,
                            "message" => "Error occurred while updating"
                        );
                    }
                }
                else{
                    $steps_filled = $this->admin->checkKYCStepsById($_POST['user_id'], 'kyc');
                    
                    $count = (int) $steps_filled->steps_filled;
                    $count++;
                    $_POST['steps_filled'] = $count;
                    if($this->user->kycupdate('kyc', $_POST, $_POST['user_id'])){
                        $response = array(
                            "status" => true,
                            "message" => "KYC Successfully updated, you will be live once your profile gets verified",
                            "steps_filled" => $count
                        );
                    }
                    else{
                        $response = array(
                            "status" => false,
                            "message" => "Error occurred while updating"
                        );
                    }
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

        public function city(){
            if(isset($_POST['name'])){
                $data = $this->admin->addData($_POST, "city");
                if($data){
                    $response = array(
                        "status" => true,
                        "id" => $this->admin->last_record('id', 'city')->id,
                        "message" => "City added successfully"
                    );
                }
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "City name is empty"
                );
            }
            echo json_encode($response);
        }

        public function banner(){
            if(isset($_FILES["banner_image"])){
                if(!empty($_FILES["banner_image"])){
                    $folder= './assets/admin/images/banner/';
                    $temp = explode(".", $_FILES["banner_image"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                    $_POST['banner_image'] = round(microtime(true)).'front.'.$temp[1];
                    move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file_img);

                    $_POST['status'] = "true";

                    $data = $this->admin->addData($_POST, "banner");

                    if($data){
                        $response = array(
                            "status" => true,
                            "message" => "Banner uploaded succesfully"
                        );
                    }
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Error occurred while uploading, image file should not be empty"
                    );
                }
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while uploading, image file is mandatory"
                );
            }

            echo json_encode($response);
        }

        public function bankdetails(){
            $received_Token = $this->input->request_headers('Authorization');
            $tokenData = $this->user->getTokenData($received_Token);

            if(isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])){

                if($this->admin->checkIfBankDetailsExistById($_POST['user_id'], 'bank_details')){
                    $response = array(
                        "status" => false,
                        "message" => "Bank details already provided"
                    );
                }
                else{
                    $flag1 = false; 
                    if(isset($_FILES["bank_cheque"])){
                        $folder= './assets/admin/images/bank_cheque/';
                        $temp = explode(".", $_FILES["bank_cheque"]["name"]);
                        $target_file_img = $folder. round(microtime(true)).'.'.$temp[1]; 
                        $_POST['bank_cheque'] = round(microtime(true)).'.'.$temp[1];
                        move_uploaded_file($_FILES["bank_cheque"]["tmp_name"], $target_file_img);
                        $flag1 = true;  
                    }
                    if($flag1){
                        $_POST['status'] = "0";
                        $data = $this->admin->addData($_POST, "bank_details");
                        if($data){
                            $response = array(
                                "status" => true,
                                "message" => "Bank details successfully added"
                            );
                        }
                        else{
                            $response = array(
                                "status" => false,
                                "message" => "Error occurred while adding"
                            );
                        }
                    }
                    else{
                        $response = array(
                            "status" => false,
                            "message" => "Bank cheque image missing"
                        );
                    }
                    
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

        public function service(){

            $data = $this->admin->addData($_POST, "services");
          
             if($data){
                echo json_encode(['status' => true, 'message' => 'Service added successfully']);
             }
             else{
                echo json_encode(['status' => false, 'message' => 'Error while adding']);
             }
        }

        public function static(){

            $data = $this->admin->addData($_POST, "static");
          
             if($data){
                echo json_encode(['status' => true, 'message' => 'Data added successfully']);
             }
             else{
                echo json_encode(['status' => false, 'message' => 'Error while adding']);
             }
        }

        public function contact(){

            $data = $this->admin->addData($_POST, "contact");
          
             if($data){
                echo json_encode(['status' => true, 'message' => 'Contact details added successfully']);
             }
             else{
                echo json_encode(['status' => false, 'message' => 'Error while adding']);
             }
        }

        public function rating(){
            $data = $this->admin->addData($_POST, "rating");
          
            if($data){
                echo json_encode(['status' => true, 'message' => 'Vendor rating added successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error while adding']);
            }
        }

        public function award(){
            if(isset($_FILES["file"])){
                if(!empty($_FILES["file"])){
                    

                    if(!isset($_POST['user_id']) || empty($_POST['user_id'])){
                        $response = array(
                            "status" => false,
                            "message" => "User ID is mandatory"
                        );
                    }
                    else{
                        $folder= './assets/admin/images/documents/';
                        $temp = explode(".", $_FILES["file"]["name"]);
                        $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                        $_POST['file'] = round(microtime(true)).'front.'.$temp[1];
                        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file_img);

                        $data = $this->admin->addData($_POST, "award");

                        if($data){
                            $response = array(
                                "status" => true,
                                "message" => "Award/Certificate uploaded succesfully"
                            );
                        }
                    } 
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Error occurred while uploading, image file should not be empty"
                    );
                }
            }
            else{
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while uploading, image file is mandatory"
                );
            }

            echo json_encode($response);
        }

        public function trainingvideo(){
            $videoCount = 0;
            $videoMissing = false;
            $training_total = $this->admin->getTrainingVideoCount()->total_videos;
            if((int)$training_total > 6){
                $response = array(
                    "status" => false,
                    "message" => "7 training videos are already there, You cannot upload more than that"
                );
            }
            else{
                if(isset($_FILES["video_file"])){
                    if(!empty($_FILES["video_file"])){
                        if($_FILES['video_file']['size'] > 12000000){
                            $response = array(
                                "status" => false,
                                "message" => "File not uploaded, File size should not exceed 12MB, Please try again"
                            );
                        }
                        else{
                            $trainingData = $this->admin->getAllTrainingVideos();

                            //If there is no data
                            if($trainingData['result'] == NULL){
                                $videoCount++;
                            }

                            //Insert video number in middle if something missing
                            foreach ($trainingData['result'] as $key => $value) {
                                $videoCount++;
                                if($videoCount != $value->video_no){
                                    if(!$this->admin->checkIfTrainingVideoAvailable($videoCount)){
                                        $videoMissing = true;
                                        break;
                                    }
                                }
                            }
                            $max_training_num = $this->admin->getMaxTrainingNumber();
                            
                            $folder= './assets/admin/videos/';
                            $temp = explode(".", $_FILES["video_file"]["name"]);
                            $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                            $_POST['video_file'] = round(microtime(true)).'front.'.$temp[1];
                            move_uploaded_file($_FILES["video_file"]["tmp_name"], $target_file_img);

                            if($videoMissing){
                                $_POST['video_no'] = $videoCount;
                            }
                            else{
                                if($max_training_num->max_video_num == NULL){
                                    $_POST['video_no'] = $videoCount;
                                }
                                else{
                                    $_POST['video_no'] = $max_training_num->max_video_num + 1;
                                }
                            }
    
                            $data = $this->admin->addData($_POST, "training");
    
                            if($data){
                                $response = array(
                                    "status" => true,
                                    "message" => "Video uploaded succesfully"
                                );
                            } 
                        }
                        
                    }
                    else{
                        $response = array(
                            "status" => false,
                            "message" => "Error occurred while uploading, video file should not be empty"
                        );
                    }
                }
                else{
                    $response = array(
                        "status" => false,
                        "message" => "Error occurred while uploading, video file is mandatory"
                    );
                }
            }
            
            echo json_encode($response);
        }
    
    }