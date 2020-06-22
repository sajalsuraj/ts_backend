<?php

require APPPATH . 'libraries/ImplementJWT.php';
defined('BASEPATH') or exit('No direct script access allowed');

class Update extends CI_Controller
{

    public function __construct()
    {
        $this->objOfJwt = new ImplementJwt();
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: access_token, Cache-Control');
        header('Access-Control-Allow-Methods: GET, HEAD, POST, PUT, DELETE');
        parent::__construct();
    }

    public function sendsms($msg, $phone)
    {

        $msg = rawurlencode($msg);    //Message Here

        $url = "http://sms99.co.in/pushsms.php?username=trjhalakr&password=incorrecthaibhai&sender=webacc&message=" . $msg . "&numbers=" . $phone;  //Store data into URL variable

        // $ret = file($url);    //Call Url variable by using file() function

        // return $ret[0];    //$ret stores the msg-id
        $ch = curl_init();

        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            )
        );

        $output = curl_exec($ch);
        return $output;
    }

    public function kyc()
    {
        if ($this->user->kyc($_POST['type'], $_POST['status'], $_POST['id'])) {
            echo json_encode(['status' => true, 'message' => "KYC verified"]);
        } else {
            echo json_encode(['status' => false, 'message' => "Not Updated"]);
        }
    }

    public function kycdata()
    {
        if(isset($_POST['id_type'])){
            if($_POST['id_type'] == "PAN"){
                if(isset($_FILES["img_front_side"]) && $_FILES["img_front_side"]["name"] != ""){
                    $folder= './assets/admin/images/documents/';
                    $temp = explode(".", $_FILES["img_front_side"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                    $_POST['img_front_side'] = round(microtime(true)).'front.'.$temp[1];
                    move_uploaded_file($_FILES["img_front_side"]["tmp_name"], $target_file_img); 
                }
            }
            else{
                if(isset($_FILES["img_front_side"]) && $_FILES["img_front_side"]["name"] != ""){
                    $folder= './assets/admin/images/documents/';
                    $temp = explode(".", $_FILES["img_front_side"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'front.'.$temp[1]; 
                    $_POST['img_front_side'] = round(microtime(true)).'front.'.$temp[1];
                    move_uploaded_file($_FILES["img_front_side"]["tmp_name"], $target_file_img);
                }
    
                if(isset($_FILES["img_back_side"]) && $_FILES["img_back_side"]["name"] != ""){
                    $folder= './assets/admin/images/documents/';
                    $temp = explode(".", $_FILES["img_back_side"]["name"]);
                    $target_file_img = $folder. round(microtime(true)).'back.'.$temp[1]; 
                    $_POST['img_back_side'] = round(microtime(true)).'back.'.$temp[1];
                    move_uploaded_file($_FILES["img_back_side"]["tmp_name"], $target_file_img); 
                }
            }
        }
        if ($this->user->kycupdate('kyc', $_POST, $_POST['user_id'])) {
            echo json_encode(['status' => true, 'message' => "KYC updated"]);
        } else {
            echo json_encode(['status' => false, 'message' => "Not Updated"]);
        }
    }

    public function send_notification($apiKey, $to, $notification, $data)
    {
        $fields = array(
            'to' => $to,
            'notification'    => $notification,
            "priority" => "high",
            "data" => $data
        );


        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server	
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_exec($ch);
        curl_close($ch);
    }

    public function userprofile()
    {
        if (isset($_FILES["face_photo"]) && $_FILES["face_photo"]["name"] != "") {
            $folder = './assets/admin/images/profile/';
            $temp = explode(".", $_FILES["face_photo"]["name"]);
            $target_file_img = $folder . round(microtime(true)) . 'face.' . $temp[1];
            $_POST['face_photo'] = round(microtime(true)) . 'face.' . $temp[1];
            move_uploaded_file($_FILES["face_photo"]["tmp_name"], $target_file_img);
        }

        if (isset($_FILES["side_face_photo"]) && $_FILES["side_face_photo"]["name"] != "") {
            $folder = './assets/admin/images/profile/';
            $temp = explode(".", $_FILES["side_face_photo"]["name"]);
            $target_file_img = $folder . round(microtime(true)) . 'sideface.' . $temp[1];
            $_POST['side_face_photo'] = round(microtime(true)) . 'sideface.' . $temp[1];
            move_uploaded_file($_FILES["side_face_photo"]["tmp_name"], $target_file_img);
        }

        if (isset($_FILES["full_body_photo"]) && $_FILES["full_body_photo"]["name"] != "") {
            $folder = './assets/admin/images/profile/';
            $temp = explode(".", $_FILES["full_body_photo"]["name"]);
            $target_file_img = $folder . round(microtime(true)) . 'fullbody.' . $temp[1];
            $_POST['full_body_photo'] = round(microtime(true)) . 'fullbody.' . $temp[1];
            move_uploaded_file($_FILES["full_body_photo"]["tmp_name"], $target_file_img);
        }

        if (isset($_FILES["tool_photo"]) && $_FILES["tool_photo"]["name"] != "") {
            $folder = './assets/admin/images/profile/';
            $temp = explode(".", $_FILES["tool_photo"]["name"]);
            $target_file_img = $folder . round(microtime(true)) . 'tool.' . $temp[1];
            $_POST['tool_photo'] = round(microtime(true)) . 'tool.' . $temp[1];
            move_uploaded_file($_FILES["tool_photo"]["tmp_name"], $target_file_img);
        }
        $id = $_POST['user_id'];
        unset($_POST['user_id']);
        if ($this->user->userupdate('worker', $_POST, $id)) {
            echo json_encode(['status' => true, 'message' => "Profile updated successfully"]);
        } else {
            echo json_encode(['status' => false, 'message' => "Nothing updated"]);
        }
    }

    public function customer(){
        $id = $_POST['id'];
        unset($_POST['id']);
        if ($this->user->userupdate('customer', $_POST, $id)) {
            echo json_encode(['status' => true, 'message' => "Profile updated successfully"]);
        } else {
            echo json_encode(['status' => false, 'message' => "Nothing updated"]);
        }
    }

    public function userabout()
    {

        if ($this->admin->checkKYCById($_POST['user_id'], 'about')) {
            if ($this->user->aboutupdate('about', $_POST, $_POST['user_id'])) {
                echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
            } else {
                echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
            }
        } else {
            $data = $this->admin->addData($_POST, 'about');
            if ($data) {
                echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
            } else {
                echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
            }
        }
    }

    public function bookingrequest()
    {

        $data['request_status'] = $_POST['request_status'];
        if ($this->admin->updatebookingrequest('request', $data, $_POST['id'], $_POST['req_no'])) {
            if ($this->admin->deletebookingrequest('request', $_POST['req_no'])) {
                $postData = array();
                $postData['req_no'] = $_POST['req_no'];
                $postData['booking_id'] = rand(10000, 99999) . "" . $_POST['id'];
                $postData['vendor_id'] = $_POST['vendor_id'];
                $postData['customer_id'] = $_POST['customer_id'];
                $postData['booking_status'] = 1;

                $locationData = array();
                $locationData['req_no'] = $_POST['req_no'];
                $locationData['lat'] = $_POST['lat'];
                $locationData['lng'] = $_POST['lng'];
                $this->admin->addData($locationData, 'vendor_booking_location');
                if ($this->admin->addData($postData, 'booking')) {
                    echo json_encode(['status' => true, 'message' => "Booking created successfully"]);
                } else {
                    echo json_encode(['status' => false, 'message' => "Error occurred while creating booking"]);
                }
            } else {
                echo json_encode(['status' => false, 'message' => "Error occurred while removing other booking requests"]);
            }
        } else {
            echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
        }
    }

    public function adminpassword()
    {
        $adminData = $this->admin->getAdminProfile($_POST['id']);
        if ($adminData->password === $this->admin->crypt($_POST['old_password'], 'e')) {
            $passwordStatus = $this->user->userupdate("worker", array("password" => $this->admin->crypt($_POST['new_password'], 'e')), $_POST['id']);
            if ($passwordStatus) {
                $response = array("message" => "Password updated", "status" => $passwordStatus);
            } else {
                $response = array("message" => "Some error occurred, please try again", "status" => $passwordStatus);
            }
        } else {
            $response = array("message" => "Old password is incorrect", "status" => false);
        }
        echo json_encode($response);
    }

    //APIs

    public function aboutme()
    {

        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {

            if ($this->admin->checkKYCById($_POST['user_id'], 'about')) {
                if ($this->user->aboutupdate('about', $_POST, $_POST['user_id'])) {
                    echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
                } else {
                    echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                }
            } else {
                $data = $this->admin->addData($_POST, 'about');
                if ($data) {
                    echo json_encode(['status' => true, 'message' => "About section updated successfully"]);
                } else {
                    echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
                }
            }
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Unauthorized Access"
            ]);
        }
    }

    public function bookingverify()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {

            $booking = $this->admin->getBookingInfo($_POST['user_id'], $_POST['booking_id']);

            if ($_POST['booking_otp'] == $booking[0]->booking_otp) {
                $otpData['is_otp_verified'] = 1;
                if ($this->admin->updateBookingStatus($otpData, $_POST['booking_id'])) {
                    $response = array(
                        "message" => "OTP verified",
                        "status" => true
                    );
                } else {
                    $response = array(
                        "message" => "Some error occurred, please try again",
                        "status" => false
                    );
                }
            } else {
                $response = array(
                    "message" => "OTP didn't match",
                    "status" => false
                );
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'worker')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }


    public function profile()
    {

        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {

            if (isset($_FILES["face_photo"])) {
                $folder = './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["face_photo"]["name"]);
                $target_file_img = $folder . round(microtime(true)) . 'face.' . $temp[1];
                $_POST['face_photo'] = round(microtime(true)) . 'face.' . $temp[1];
                move_uploaded_file($_FILES["face_photo"]["tmp_name"], $target_file_img);
            }

            if (isset($_FILES["side_face_photo"])) {
                $folder = './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["side_face_photo"]["name"]);
                $target_file_img = $folder . round(microtime(true)) . 'sideface.' . $temp[1];
                $_POST['side_face_photo'] = round(microtime(true)) . 'sideface.' . $temp[1];
                move_uploaded_file($_FILES["side_face_photo"]["tmp_name"], $target_file_img);
            }

            if (isset($_FILES["full_body_photo"])) {
                $folder = './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["full_body_photo"]["name"]);
                $target_file_img = $folder . round(microtime(true)) . 'fullbody.' . $temp[1];
                $_POST['full_body_photo'] = round(microtime(true)) . 'fullbody.' . $temp[1];
                move_uploaded_file($_FILES["full_body_photo"]["tmp_name"], $target_file_img);
            }

            if (isset($_FILES["tool_photo"])) {
                $folder = './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["tool_photo"]["name"]);
                $target_file_img = $folder . round(microtime(true)) . 'tool.' . $temp[1];
                $_POST['tool_photo'] = round(microtime(true)) . 'tool.' . $temp[1];
                move_uploaded_file($_FILES["tool_photo"]["tmp_name"], $target_file_img);
            }
            $id = $_POST['user_id'];
            unset($_POST['user_id']);
            if ($this->user->userupdate('worker', $_POST, $id)) {
                $userdata = $this->user->getProfileData($id);
                if ($userdata->face_photo != "") {
                    $userdata->face_photo = base_url() . "assets/admin/images/profile/" . $userdata->face_photo;
                }

                if ($userdata->side_face_photo != "") {
                    $userdata->side_face_photo = base_url() . "assets/admin/images/profile/" . $userdata->side_face_photo;
                }

                if ($userdata->full_body_photo != "") {
                    $userdata->full_body_photo = base_url() . "assets/admin/images/profile/" . $userdata->full_body_photo;
                }

                if ($userdata->tool_photo != "") {
                    $userdata->tool_photo = base_url() . "assets/admin/images/profile/" . $userdata->tool_photo;
                }
                echo json_encode(['status' => true, 'data' => $userdata, 'message' => "Profile updated successfully"]);
            } else {
                echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
            }
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Unauthorized Access"
            ]);
        }
    }

    public function otp()
    {

        if ($this->user->userOTPVerified('worker', $_POST['otp_verified'], $_POST['phone'])) {
            echo json_encode(['status' => true, 'message' => "User verified"]);
        } else {
            echo json_encode(['status' => false, 'message' => "Error occurred while verification"]);
        }
    }



    public function customerprofile()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            if (isset($_FILES["photo"])) {
                $folder = './assets/admin/images/profile/';
                $temp = explode(".", $_FILES["photo"]["name"]);
                $target_file_img = $folder . round(microtime(true)) . '.' . $temp[1];
                $_POST['photo'] = round(microtime(true)) . '.' . $temp[1];
                move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file_img);
            }

            $id = $_POST['user_id'];
            unset($_POST['user_id']);
            if ($this->user->userupdate('customer', $_POST, $id)) {
                $userdata = $this->customer->getProfileData($id);
                if ($userdata->photo != "") {
                    $userdata->photo = base_url() . "assets/admin/images/profile/" . $userdata->photo;
                }
                echo json_encode(['status' => true, 'data' => $userdata, 'message' => "Profile updated successfully"]);
            } else {
                echo json_encode(['status' => false, 'message' => "Error occurred while updating"]);
            }
        }
    }

    public function userbookingrequest()
    {

        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['vendor_id'])) {

            if ($this->admin->checkBookingByReqNo($_POST['req_no'])) {
                $response = array(
                    "status" => false,
                    "message" => "This booking request is already accepted"
                );
            } else {
                date_default_timezone_set('Asia/Kolkata');
                $data['accepted_at'] = time();
                $data['request_status'] = $_POST['request_status'];
                if ($this->admin->updatebookingrequest('request', $data, $_POST['id'], $_POST['req_no'])) {
                    if ($this->admin->deletebookingrequest('request', $_POST['req_no'])) {
                        $postData = array();
                        $postData['req_no'] = $_POST['req_no'];
                        $postData['booking_id'] = rand(10000, 99999) . "" . $_POST['id'];
                        $postData['vendor_id'] = $_POST['vendor_id'];
                        $postData['customer_id'] = $_POST['customer_id'];
                        $postData['booking_otp'] = rand(1000, 9999) . "" . $_POST['id'];
                        $postData['booking_status'] = 1;
                        $postData['created_at'] = time();

                        $locationData = array();
                        $locationData['req_no'] = $_POST['req_no'];
                        $locationData['lat'] = $_POST['lat'];
                        $locationData['lng'] = $_POST['lng'];

                        $this->admin->addData($locationData, 'vendor_booking_location');
                        if ($this->admin->addData($postData, 'booking')) {
                            $vendor = $this->user->getProfileData($_POST['vendor_id']);
                            $customer = $this->user->getCustomerData($_POST['customer_id']);
                            $api_key = "AAAAQgv_Zag:APA91bGGYsWdrhoPDxgNrNP-FSn30esdz3oyccqMAMXX1ym0Cl7yB6XcAxIr8oWKQ0tDV5hzS0tV5fxduMIlcqTvT2IwjytTCAlzVOEE-K54pvggi0a9DEGxmyVcZfyFDXIVzG5HBuxa";
                            $to = $customer->device_id;
                            $notificationMsg = array(
                                'body'     => $vendor->name . ' has accepted your booking request',
                                'title'    => 'Troubleshooter',
                            );
                            $bodyData = array(
                                'action' => "request_accepted"
                            );
                            $this->send_notification($api_key, $to, $notificationMsg, $bodyData);
                            $response = array('status' => true, 'message' => "Booking created successfully");
                        } else {
                            $response = array('status' => false, 'message' => "Error occurred while creating booking");
                        }
                    } else {
                        $response = array('status' => false, 'message' => "Error occurred while removing other booking requests");
                    }
                } else {
                    $response = array('status' => false, 'message' => "Error occurred while updating");
                }
            }
        } else {
            if ($this->admin->checkUserById($_POST['vendor_id'], 'worker')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function vendorlocation()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            $data['lat'] = $_POST['lat'];
            $data['lng'] = $_POST['lng'];
            if ($this->admin->updatevendorlocation($data, $_POST['req_no'])) {
                $response = array('status' => true, 'message' => "Location updated");
            } else {
                $response = array('status' => false, 'message' => "Error occurred while updating");
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'worker')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function cancelbooking()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);
        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            if ($_POST['booking_status'] == "" || $_POST['booking_status'] != "4") {
                $response = array(
                    "status" => false,
                    "message" => "Invalid booking status"
                );
            } else {
                $data['booking_status'] = $_POST['booking_status'];
                $data['reason_to_cancel'] = $_POST['reason_to_cancel'];
                if ($this->admin->updateBookingStatus($data, $_POST['booking_id'])) {
                    $message = "Booking Cancelled";
                    $response = array('status' => true, 'message' => $message);
                } else {
                    $response = array('status' => false, 'message' => "Error occurred while updating");
                }
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'worker')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    function isParent($service)
    {
        $serviceData = $this->admin->getServiceById($service);
        if ($serviceData->level == "1") {
            return true;
        }
        return false;
    }

    function isParentLevel2($service)
    {
        $serviceData = $this->admin->getServiceById($service);
        if ($serviceData->level == "2") {
            return true;
        }
        return false;
    }

    function getParentService($service)
    {
        $serviceData = $this->admin->getServiceById($service);
        if ($serviceData->level == "1") {
            return $serviceData;
        }
        return $this->getParentService($serviceData->parent_category);
    }

    function getParentServiceLevel2($service)
    {
        $serviceData = $this->admin->getServiceById($service);
        if ($serviceData->level == "2") {
            return $serviceData;
        }
        return $this->getParentServiceLevel2($serviceData->parent_category);
    }

    public function bookingstatus()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);
        date_default_timezone_set('Asia/Kolkata');
        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            $data['booking_status'] = $_POST['booking_status'];
            if ($this->admin->updateBookingStatus($data, $_POST['booking_id'])) {
                $message = "";
                if ($_POST['booking_status'] == 2) {
                    $message = "Task started/In Progress";
                    $patchData['started_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                } else if ($_POST['booking_status'] == 3) {
                    $message = "Task paused";
                    $patchData['paused_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                } else if ($_POST['booking_status'] == 4) {
                    $message = "Booking Cancelled";
                } else if ($_POST['booking_status'] == 5) {
                    $message = "Task completed";

                    //Bill calculation logic
                    $services = $this->admin->getServicesFromBooking($_POST['booking_id']);
                    $started_at = $services[0]->started_at;
                    $services = $services[0]->services;

                    $allServices = explode(",", $services);
                    $timeEnd = new DateTime('now');
                    $timeStart = new DateTime('@' . $started_at);
                    $interval = $timeStart->diff($timeEnd);
                    $interval = $interval->format('%H,%i');
                    $intervalArr = explode(",", $interval);
                    $totalTimeInMinutes = ((int) $intervalArr[0] * 60) + ((int) $intervalArr[1]);
                    $totalBill = 0;
                    $package_applied = "";
                    $membership_applied = "";
                    $priceForSingle = 0;

                    //Package Calculation
                    foreach ($allServices as $service) {

                        if (count($this->admin->checkIfUserHasPackage($_POST['user_id'])) > 0) {
                            $timeNow = date_create(date("Y-m-d"));
                            $packages = $this->admin->checkIfUserHasPackage($_POST['user_id']);
                            foreach ($packages as $p) {
                                $package = $this->admin->packageById($p->package_id);
                                $timeEnd = date_create($package->to_date);
                                $diff = date_diff($timeEnd, $timeNow);
                                $interval = $diff->format("%R%a");
                                if ($interval < 0) {
                                    $packageServices = json_decode($package->services, true);
                                    $package_applied .= $package->id . ",";
                                    foreach ($packageServices as $ms) {
                                        if ($this->isParent($ms['service'])) {
                                            if ($this->getParentService($service)->id == $ms['service']) {

                                                if ($ms['mode'] == "rate_per_min") {
                                                    $priceForSingle = $totalTimeInMinutes * (int) $ms['price'];
                                                    $totalBill += $totalTimeInMinutes * (int) $ms['price'];
                                                } else if ($ms['mode'] == "fixed") {
                                                    $priceForSingle = (int) $ms['price'];
                                                    $totalBill += (int) $ms['price'];
                                                }
                                            }
                                        } else if ($this->isParentLevel2($ms['service'])) {
                                            if ($this->getParentServiceLevel2($service)->id == $ms['service']) {

                                                if ($ms['mode'] == "rate_per_min") {
                                                    $priceForSingle = $totalTimeInMinutes * (int) $ms['price'];
                                                    $totalBill += $totalTimeInMinutes * (int) $ms['price'];
                                                } else if ($ms['mode'] == "fixed") {
                                                    $priceForSingle = (int) $ms['price'];
                                                    $totalBill += (int) $ms['price'];
                                                }
                                            }
                                        } else {
                                            if ($service == $ms['service']) {

                                                if ($ms['mode'] == "rate_per_min") {
                                                    $priceForSingle = $totalTimeInMinutes * (int) $ms['price'];
                                                    $totalBill += $totalTimeInMinutes * (int) $ms['price'];
                                                } else if ($ms['mode'] == "fixed") {
                                                    $priceForSingle = (int) $ms['price'];
                                                    $totalBill += (int) $ms['price'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $priceForSingle = $totalTimeInMinutes * (int) $this->admin->getServiceRate($service)->rate_per_min;
                            $totalBill += $totalTimeInMinutes * (int) $this->admin->getServiceRate($service)->rate_per_min;
                        }
                    }

                    if (count($this->admin->checkIfUserHasMembership($_POST['user_id'])) > 0) {
                        $timeNow = date_create(date("Y-m-d"));
                        $memberships = $this->admin->checkIfUserHasMembership($_POST['user_id']);
                        foreach ($memberships as $mem) {
                            $membership = $this->admin->membershipById($mem->membership_id);
                            $timeEnd = date_create($membership->to_date);
                            $diff = date_diff($timeEnd, $timeNow);
                            $interval = $diff->format("%R%a");
                            if ($interval < 0) {
                                $memberServices = json_decode($membership->services, true);
                                $membership_applied .= $mem->membership_id . ",";
                                foreach ($memberServices as $ms) {
                                    foreach ($allServices as $service) {
                                        if ($this->isParent($ms['service'])) {
                                            if ($this->getParentService($service)->id == $ms['service']) {

                                                $totalBill = $totalBill - $priceForSingle;
                                            }
                                        } else if ($this->isParentLevel2($ms['service'])) {
                                            if ($this->getParentServiceLevel2($service)->id == $ms['service']) {

                                                $totalBill = $totalBill - $priceForSingle;
                                            }
                                        } else {
                                            if ($service == $ms['service']) {

                                                $totalBill = $totalBill - $priceForSingle;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($membership_applied != "") {
                        $membership_applied = rtrim($membership_applied, ",");
                    }
                    if ($package_applied != "") {
                        $package_applied = rtrim($package_applied, ",");
                    }
                    $patchData['membership'] = $membership_applied;
                    $patchData['package'] = $package_applied;
                    $patchData['amount'] = $totalBill;
                    $patchData['completed_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                } else if ($_POST['booking_status'] == 6) {
                    $message = "Task Restarted";
                    $patchData['restarted_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                }
                $response = array('status' => true, 'message' => $message);
            } else {
                $response = array('status' => false, 'message' => "Error occurred while updating");
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'worker')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function bills()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            if (isset($_FILES["bill_image"])) {
                $folder = './assets/admin/images/bills/';
                $temp = explode(".", $_FILES["bill_image"]["name"]);
                $target_file_img = $folder . round(microtime(true)) . 'bill.' . $temp[1];
                $data['bill_image'] = round(microtime(true)) . 'bill.' . $temp[1];
                move_uploaded_file($_FILES["bill_image"]["tmp_name"], $target_file_img);
            }

            $data['bill_amount'] = $_POST['bill_amount'];
            $added_amount = 0;
            $service_charge = 0;
            $added_bill_amount = $_POST['bill_amount'];

            if ($added_bill_amount == "null" || $added_bill_amount == "") {
            } else {
                $added_amount_json = json_decode($added_bill_amount, true);

                foreach ($added_amount_json as $key => $value) {
                    $added_amount += $value;
                }

                if ($added_amount > 0) {
                    $service_charge =  (float) $added_amount * 0.18;
                    $data['service_charge_added'] = 1;
                }
            }
            $bookingData = $this->admin->getBookingByID($_POST['booking_id']);
            $totalBill = $bookingData->amount;
            $totalBill = $totalBill + $service_charge;

            if ($this->admin->updateBookingStatus($data, $_POST['booking_id'])) {
                $response = array('status' => true, 'message' => 'Booking data updated');
            } else {
                $response = array('status' => false, 'message' => "Error occurred while updating");
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'worker')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function customerbookingstatus()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            $data['booking_status'] = $_POST['booking_status'];
            if ($this->admin->updateBookingStatus($data, $_POST['booking_id'])) {
                $message = "";
                $booking_detail = $this->admin->getBookingInfoByCustomerID($_POST['user_id'], $_POST['booking_id']);
                $vendor = $this->user->getProfileData($booking_detail[0]->vendor_id);
                $apiKey = "AAAA0W6cR-g:APA91bGr4S_9LPdoWVc9k3aY5_6Nh3e_orRbsj6dLOq59nAC5GmLS9-21Au2figrAoCu9VjrsgWsd3taKiPvj2s2-niwWGDGA0B5KGGjFdCCZMQMcKdelOcexyXyuNcmcm_iRW9qGEJr";
                if ($_POST['booking_status'] == 2) {
                    $message = "Task started/In Progress";
                    $patchData['started_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                } else if ($_POST['booking_status'] == 3) {
                    $message = "Task paused";
                    $patchData['paused_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                } else if ($_POST['booking_status'] == 4) {
                    $to = $vendor->device_id;
                    $notificationMsg = array(
                        'body'     => $booking_detail[0]->customer_name . ' has cancelled the booking!!',
                        'title'    => '[Troubleshooter]:Booking Cancelled!',
                    );
                    $bodyData = array(
                        'action' => "booking_cancelled"
                    );
                    $this->send_notification($apiKey, $to, $notificationMsg, $bodyData);
                    $message = "Booking Cancelled";
                } else if ($_POST['booking_status'] == 5) {
                    $message = "Task completed";
                    //Bill calculation logic
                    $services = $this->admin->getServicesFromBooking($_POST['booking_id']);
                    $started_at = $services[0]->started_at;
                    $services = $services[0]->services;

                    $allServices = explode(",", $services);
                    $timeEnd = new DateTime('now');
                    $timeStart = new DateTime('@' . $started_at);
                    $interval = $timeStart->diff($timeEnd);
                    $interval = $interval->format('%H,%i');
                    $intervalArr = explode(",", $interval);
                    $totalTimeInMinutes = ((int) $intervalArr[0] * 60) + ((int) $intervalArr[1]);
                    $totalBill = 0;

                    foreach ($allServices as $service) {
                        $totalBill += $totalTimeInMinutes * (int) $this->admin->getServiceRate($service)->rate_per_min;
                    }

                    $patchData['amount'] = $totalBill;
                    $patchData['completed_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                } else if ($_POST['booking_status'] == 6) {
                    $message = "Task Restarted";
                    $patchData['restarted_at'] = time();
                    $this->admin->updateBookingStatus($patchData, $_POST['booking_id']);
                }
                $response = array('status' => true, 'message' => $message);
            } else {
                $response = array('status' => false, 'message' => "Error occurred while updating");
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'customer')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function bookingpaymentupdate()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            $data['has_paid'] = "1";
            if ($this->admin->updateBookingStatus($data, $_POST['booking_id'])) {
                $message = "Payment done";
                $response = array('status' => true, 'message' => $message);
            } else {
                $response = array('status' => false, 'message' => "Error occurred while updating");
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'customer')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function vendortokenupdate()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            if ($this->user->userupdate("worker", array("device_id" => $_POST['token']), $_POST['user_id'])) {
                $response = array(
                    "status" => true,
                    "message" => "Token updated successfully"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "Error occurred while updating token, kindly restart the app"
                );
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'worker')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function customerinfo()
    {
        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
            $id = $_POST['user_id'];
            unset($_POST['user_id']);
            if ($this->user->userupdate('customer', $_POST, $id)) {
                $response = array(
                    "status" => true,
                    "message" => "User info updated"
                );
            } else {
                $response = array(
                    "status" => true,
                    "message" => "Error occurred while updating"
                );
            }
        } else {
            if ($this->admin->checkUserById($_POST['user_id'], 'customer')) {
                $response = array(
                    "status" => false,
                    "message" => "Unauthorized Access"
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "User doesn't exist"
                );
            }
        }
        echo json_encode($response);
    }

    public function vendorpassword()
    {
        $phone = $_POST['phone'];
        unset($_POST['phone']);
        $_POST['password'] = $this->admin->crypt($_POST['password'], 'e');
        if ($this->user->userupdatebyphone('worker', $_POST, $phone)) {

            $user = $this->admin->getUserByPhone($phone, "worker");
            $msg = "Your password has been updated successfully";

            $headers = "From: noreply@troubleshooters.services" . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            // send email

            $mail = mail($user->email, "Password updated - Troubleshooters", $msg, $headers);

            if (!$mail) {
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
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating password"
            );
        }
        echo json_encode($response);
    }

    public function customerpassword()
    {
        $phone = $_POST['phone'];
        unset($_POST['phone']);
        $_POST['password'] = $this->admin->crypt($_POST['password'], 'e');
        if ($this->user->userupdatebyphone('customer', $_POST, $phone)) {

            $user = $this->admin->getUserByPhone($phone, "customer");
            $msg = "Your password has been updated successfully";

            $headers = "From: noreply@troubleshooters.services" . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            // send email

            $mail = mail($user->email, "Password updated - Troubleshooters", $msg, $headers);

            if (!$mail) {
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
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating password"
            );
        }
        echo json_encode($response);
    }

    public function service()
    {
        $service_id = $_POST['id'];
        unset($_POST['id']);
        if ($this->user->userupdate('services', $_POST, $service_id)) {
            $response = array(
                "status" => true,
                "message" => "Service Updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating service"
            );
        }
        echo json_encode($response);
    }

    public function contact()
    {
        $id = $_POST['id'];
        unset($_POST['id']);
        if ($this->user->userupdate('contact', $_POST, $id)) {
            $response = array(
                "status" => true,
                "message" => "Contact us updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating contact"
            );
        }
        echo json_encode($response);
    }

    public function banner()
    {
        $banner_id = $_POST['id'];
        unset($_POST['id']);
        if ($this->user->userupdate('banner', $_POST, $banner_id)) {
            $response = array(
                "status" => true,
                "message" => "Banner status Updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating banner status"
            );
        }
        echo json_encode($response);
    }

    public function city()
    {
        $banner_id = $_POST['id'];
        unset($_POST['id']);
        if ($this->user->userupdate('city', $_POST, $banner_id)) {
            $response = array(
                "status" => true,
                "message" => "City name updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating city name"
            );
        }
        echo json_encode($response);
    }

    public function static()
    {
        if ($this->user->staticupdate('static', $_POST, $_POST['type'])) {
            $response = array(
                "status" => true,
                "message" => "Data updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating"
            );
        }
        echo json_encode($response);
    }

    public function referral()
    {
        $amount_id = $_POST['id'];
        $_POST['created_at'] = time();
        unset($_POST['id']);
        if ($this->user->userupdate('referral', $_POST, $amount_id)) {
            $response = array(
                "status" => true,
                "message" => "Amount updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating"
            );
        }
        echo json_encode($response);
    }

    public function homepage()
    {
        if ($this->user->userupdate('homepage', $_POST, $_POST['id'])) {
            $response = array(
                "status" => true,
                "message" => "Data updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating"
            );
        }
        echo json_encode($response);
    }

    public function package(){
        if(isset($_FILES["image"])){
            if($_FILES["image"]["error"] !== 4){
                $folder= './assets/admin/images/';
                $temp = explode(".", $_FILES["image"]["name"]);
                $target_file_img = $folder. round(microtime(true)).'package.'.$temp[1]; 
                $_POST['image'] = round(microtime(true)).'package.'.$temp[1];
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file_img);  
            }
        }
        $id = $_POST['id'];
        unset($_POST['id']);
 
        if ($this->user->userupdate('packages', $_POST, $id)) {
            $response = array(
                "status" => true,
                "message" => "Package updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Nothing updated"
            );
        }
        echo json_encode($response);
    }

    public function membership(){
        if(isset($_FILES["image"])){
            if($_FILES["image"]["error"] !== 4){
                $folder= './assets/admin/images/';
                $temp = explode(".", $_FILES["image"]["name"]);
                $target_file_img = $folder. round(microtime(true)).'package.'.$temp[1]; 
                $_POST['image'] = round(microtime(true)).'package.'.$temp[1];
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file_img);  
            }
        }
        $id = $_POST['id'];
        unset($_POST['id']);
 
        if ($this->user->userupdate('membership', $_POST, $id)) {
            $response = array(
                "status" => true,
                "message" => "Membership updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Nothing updated"
            );
        }
        echo json_encode($response);
    }

    public function faq()
    {
        $id = $_POST['id'];
        unset($_POST['id']);
        if ($this->user->userupdate('faq', $_POST, $id)) {
            $response = array(
                "status" => true,
                "message" => "Data updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating"
            );
        }
        echo json_encode($response);
    }

    public function faqtitle()
    {
        $id = $_POST['id'];
        unset($_POST['id']);
        if ($this->user->userupdate('faq_title', $_POST, $id)) {
            $response = array(
                "status" => true,
                "message" => "Data updated"
            );
        } else {
            $response = array(
                "status" => false,
                "message" => "Error occurred while updating"
            );
        }
        echo json_encode($response);
    }

    public function usertraining()
    {

        $received_Token = $this->input->request_headers('Authorization');
        $tokenData = $this->user->getTokenData($received_Token);

        if (isset($_POST['user_id']) && isset($_POST['training_video_no'])) {
            if (isset($tokenData['user_id']) && ($tokenData['user_id'] == $_POST['user_id'])) {
                $id = $_POST['user_id'];
                unset($_POST['user_id']);

                $last_seen_video = $this->user->getLastSeenVideo($id);
                $last_seen_video = $last_seen_video->training_video_no;

                if ((int) $last_seen_video >= (int) $_POST['training_video_no']) {
                    $response = array(
                        "status" => false,
                        "message" => "This video is already seen by the vendor",
                        "status_code" => 1
                    );
                } else {
                    if ($this->user->userupdate('worker', $_POST, $id)) {
                        $response = array(
                            "status" => true,
                            "message" => "Vendor data updated",
                            "status_code" => 2
                        );
                    } else {
                        $response = array(
                            "status" => false,
                            "message" => "Error occurred while updating"
                        );
                    }
                }
            } else {
                if ($this->admin->checkUserById($_POST['user_id'], 'worker')) {
                    $response = array(
                        "status" => false,
                        "message" => "Unauthorized Access"
                    );
                } else {
                    $response = array(
                        "status" => false,
                        "message" => "User doesn't exist"
                    );
                }
            }
        } else {
            $response = array(
                "status" => false,
                "message" => "Data required, post data cannot be empty"
            );
        }

        echo json_encode($response);
    }
}
