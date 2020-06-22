<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');

    class User extends CI_Model{ 
        function __construct(){
            parent::__construct();
        }

        public function login($data){
            $this->db->select('id, name, phone, type');
            $query = $this->db->get_where('worker', array('phone' => $data['phone'], 'password' => $data['password']))->row();
            return $query;
        }

        public function getTokenData($received_Token){
            try
            {
                if ( "OPTIONS" === $_SERVER['REQUEST_METHOD'] ) {
                    die();
                }
                if(isset($received_Token['access_token'])){
                    $jwtData = $this->objOfJwt->DecodeToken($received_Token['access_token']);
                }
                else{
                    $jwtData = "";
                }
                
                return $jwtData;
            }
            catch (Exception $e)
            {
                http_response_code('401');
                return array( "status" => false, "message" => $e->getMessage());exit;
            }
        }

        public function getProfileData($data){
            $this->db->select('id, name, email, city, phone, face_photo, side_face_photo, full_body_photo, tool_photo, otp_verified, work_location, primary_profession, sub_profession, mode_of_transport, device_id, created_at');
            $query = $this->db->get_where('worker', array('id' => $data))->row();
            return $query;
        }

        public function getCustomerData($data){
            $this->db->select('id, name, email, phone, photo, otp_verified, referral, device_id');
            $query = $this->db->get_where('customer', array('id' => $data))->row();
            return $query;
        }

        public function getCustomersWhoAreReferred(){
            $this->db->select('c.name as referred_name, c1.name as referred_by_name, c.id as referred_id, c1.id as referred_by_id, c.referred_by as referral_code');
            $this->db->from('customer as c');
            $this->db->join('customer as c1', 'c.referred_by = c1.referral','left');
            $this->db->where('c.referred_by <> ""');
            $query = $this->db->get();
            return $query->result();
        }

        public function getLastSeenVideo($id){
            $this->db->select('training_video_no');
            $query = $this->db->get_where('worker', array('id' => $id))->row();
            return $query;
        }

        public function kyc($table, $data, $id){
            $data = array(
                'is_verified' => $data
             );
            $this->db->where('id', $id);
            $this->db->update($table, $data); 
            return true;
        }

        public function kycupdate($table, $data, $id){
            $this->db->where('user_id', $id);
            $this->db->update($table, $data); 
            return true;
        }

        public function userupdate($table, $data, $id){
            $this->db->where('id', $id);
            $this->db->update($table, $data); 
            return ($this->db->affected_rows() > 0) ? true : false; 
        }

        public function staticupdate($table, $data, $type){
            $this->db->where('type', $type);
            $this->db->update($table, $data); 
            return true;
        }

        public function userupdatebyphone($table, $data, $phone){
            $this->db->where('phone', $phone);
            $this->db->update($table, $data); 
            return true;
        }

        public function aboutupdate($table, $data, $id){
            $this->db->where('user_id', $id);
            $this->db->update($table, $data); 
            return true;
        }

        public function updateUserIfVerified($table, $data, $phone){
            $this->db->where('phone', $phone);
            $this->db->update($table, $data); 
            return true;
        }

        public function updateUserIfByEmail($table, $data, $email){
            $this->db->where('email', $email);
            $this->db->update($table, $data); 
            return ($this->db->affected_rows() > 0) ? true : false; 
        }

        public function checkUserOtp($phone, $otp, $type){
            $this->db->select('*');
            $query = $this->db->get_where($type, array('phone' => $phone, 'otp' => $otp));
            return $query->num_rows() > 0 ? true : false;
        }

        public function deleteOTP($table, $id){
            $res = $this->db->delete($table, array('phone' => $id)); 
            return $res;
        }

        public function sendFCM($api_key, $message, $id, $message_info='') {

            $API_ACCESS_KEY = $api_key;
        
            $url = 'https://fcm.googleapis.com/fcm/send';
        
            $fields = array (
                    'registration_ids' => array (
                            $id
                    ),
                    'data' => array (
                            "message" => $message,
                            'message_info' => $message_info,
                    ),                
                    'priority' => 'high'
                    // 'notification' => array(
                    //             'title' => $message['title'],
                    //             'body' => $message['body'],                            
                    // ),
            );
            $fields = json_encode ( $fields );
        
            $headers = array (
                    'Authorization: key=' . $API_ACCESS_KEY,
                    'Content-Type: application/json'
            );
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_POST, true );
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
            $result = curl_exec ( $ch );
            curl_close ( $ch );
            return $result;
        }

    }

?>