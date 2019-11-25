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

    }

?>