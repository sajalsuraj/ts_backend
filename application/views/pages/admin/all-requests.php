<?php
  if($this->session->has_userdata('type') == true){
    if($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin"){

    }
    else{
      redirect('users/login'); 
    }
  }
  else{
    redirect('users/login');
  }
?> 
<style>
.table{
    font-size: 12px;
}
.grey-font{
    color: grey;
}
.red-font{
    color: red;
}
.green-font{
    color:green;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Service Requests</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allRequests = $this->admin->getAllRequests();
?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Request No.</th>
                    <th>Customer Name</th>
                    <th>Vendor Assigned</th>
                    <th>Services Requested</th>
                    <th>Assigned vendors</th>
                    <th>Location</th>
                    <th>Request status</th>
                    <th>Accepted At</th>
                    <th>Created At</th> 
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allRequests as $req) { ?>
                <tr>
                    <td><b><?php echo $req->req_no; ?></b></td>
                    <td><?php echo $req->customer_name; ?></td>
                    <td><?php echo $req->vendor_name; ?></td>
                    <td><b><i>
                    <?php 
                     if($req->services != ""){
                        $tempServices = explode(",", $req->services);
                        $serviceArr = [];
                        foreach($tempServices as $ser){
                            $serviceArr[] = $this->admin->getServiceById($ser);
                        }
                        $serString = "";
                        foreach($serviceArr as $ser){
                            $serString .= $ser->service_name.", ";
                        }
                        echo rtrim($serString, ", ");
                     }
                    ?></i></b>
                    </td>
                    <td>
                    <?php 
                     if($req->last_assigned_to != ""){
                        $tempVendors = explode(",", $req->last_assigned_to);
                        $vendorArr = [];
                        foreach($tempVendors as $vendor){
                            $vendorArr[] = $this->user->getProfileData($vendor);
                        }
                        $vendorString = "";
                        foreach($vendorArr as $vendor){
                            $vendorString .= $vendor->name.", ";
                        }
                        echo rtrim($vendorString, ", ");
                     }
                    ?>
                    </td>
                    <td>
                     <a target="_blank" href="https://www.google.com/search?q=<?php echo $req->lat."+".$req->lng; ?>"><?php echo $req->lat.", ".$req->lng; ?></a>
                    </td>
                    <td>
                    <?php 
                        $status = "";
                        if($req->request_status == "0"){
                            $status = "Not yet accepted";
                        }
                        else if($req->request_status == "1"){
                            $status = "Accepted";
                        }
                        else{
                            $status = "Cancelled";
                        }
                    ?>
                    <b><span class="<?php if($req->request_status == "0"){echo "grey-font";}else if($req->request_status == "1"){echo "green-font";}else{echo "red-font";} ?>"><?php echo $status; ?></span></b>
                    </td>
                    <td>
                        <?php 
                            if($req->accepted_at != ""){
                                $timeAccepted = new DateTime('@'.$req->accepted_at);
                                echo $timeAccepted->format('Y-m-d H:i');
                            }  
                        ?>
                    </td>
                    <td>
                    <?php 
                            if($req->created_at != ""){
                                $timeCreated = new DateTime('@'.$req->created_at);
                                echo $timeCreated->format('Y-m-d H:i');
                            }  
                    ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $('#dataTable').DataTable();
</script>