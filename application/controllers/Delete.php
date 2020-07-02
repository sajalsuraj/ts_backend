<?php

    require APPPATH . 'libraries/ImplementJWT.php';
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Delete extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->objOfJwt = new ImplementJwt();
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: access_token, Cache-Control');
            header('Access-Control-Allow-Methods: GET, HEAD, POST, PUT, DELETE');
        }

        public function user(){
            if($this->admin->deleteEntity($_POST['type'], $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'User deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Unable to delete']);
            }
        }

        public function deleteservice($id){
            $data = $this->admin->checkIfServiceIsParent($id);
            if($data){
                foreach ($data as $row)
                {
                    $this->deleteservice($row->id);
                }
            }

            $this->admin->deleteEntity('services', $id);
            if(!$data){
                echo json_encode(['status' => true, 'message' => 'Service successfully deleted']);
            }
        }

        public function service(){
            $this->deleteservice($_POST['id']);
        }

        public function partner(){
            if($this->admin->deleteEntity("partners", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Partner deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function admin(){
            if($this->admin->deleteEntity("worker", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Admin deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function notification(){
            if($this->admin->deleteEntity("admin_notification", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Notification deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function city(){
            if($this->admin->deleteEntity("city", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'City deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function vehicle(){
            if($this->admin->deleteEntity("vehicle", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Vehicle deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function trainingvideos(){
            if($this->admin->deleteEntity("training", $_POST['id'])){
                $path = getcwd().'/assets/admin/videos/'.$_POST['video_file'];
                unlink($path);
                echo json_encode(['status' => true, 'message' => 'Training video deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function award(){
            if($this->admin->deleteEntity("award", $_POST['id'])){
                if(file_exists(getcwd().'/assets/admin/images/documents/'.$_POST['file'])){
                    $path = getcwd().'/assets/admin/images/documents/'.$_POST['file'];
                    unlink($path);
                }
                echo json_encode(['status' => true, 'message' => 'Award deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function package(){
            if($this->admin->deleteEntity("packages", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Package deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function membership(){
            if($this->admin->deleteEntity("membership", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Membership deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function faqcontent(){
            if($this->admin->deleteEntity("faq", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'FAQ deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function about(){
            if($this->admin->deleteEntity("about", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Partner business details deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function bankdetail(){
            if($this->admin->deleteEntity("bank_details", $_POST['id'])){
                echo json_encode(['status' => true, 'message' => 'Partner bank details deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

        public function faqtitle(){
            if($this->admin->deleteEntity("faq_title", $_POST['id'])){
                $this->admin->deletefaqcontentbytitle($_POST['id']);
                echo json_encode(['status' => true, 'message' => 'FAQ Title deleted & All related FAQ QAs deleted successfully']);
            }
            else{
                echo json_encode(['status' => false, 'message' => 'Error occurred, Unable to delete']);
            }
        }

    }

?>