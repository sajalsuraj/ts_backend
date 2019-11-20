<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "worker") {

    } else {
        redirect('users/dashboard');
    }
} else {
    redirect('users/login');
}
?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Requests</a>
    </li>
    <li class="breadcrumb-item active">List</li>
</ol>

<?php  $requests = $this->admin->getAllActiveRequests($this->session->userdata('user_id')); ?>

<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <th>Request No.</th>
                <th>Customer Name</th>
                <th>Status</th>
                <th>Action</th>
            </thead>
            <tbody>
            <?php foreach ($requests as $request) { ?>
                <tr>
                    <td><?php echo $request->req_no; ?></td>
                    <td><?php echo $request->customer_name; ?></td>
                    <td><?php if($request->request_status == 0){echo "Pending";}else if($request->request_status == 1){echo "Accepted";} ?></td>
                    <td>
                        <button <?php if($request->request_status == 1){echo "disabled";} ?> id="req_<?php echo $request->id; ?>_<?php echo $request->req_no; ?>_<?php echo $request->vendor_id; ?>_<?php echo $request->customer_id; ?>" type="button" class="btn btn-success btn-accept">Accept</button>
                    </td>
                </tr>  
            <?php } ?>      
            </tbody>
        </table>
    </div>
</div>
<script>
var loc = {lat:"", lng:""};
function geoFindMe() {
    var options = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    };
    function success(position) {
        loc.lat = position.coords.latitude;
        loc.lng = position.coords.longitude;
        var detail = $('.btn-accept').attr('id'), status = 1;
        var obj = {id:detail.split('_')[1], lat:loc.lat, lng:loc.lng, request_status:status, req_no:detail.split('_')[2], vendor_id:detail.split('_')[3], customer_id:detail.split('_')[4]};
        $.ajax({
            url:'<?php echo base_url(); ?>update/bookingrequest',
            type: 'POST',
            data: obj,
            dataType:'json',
            success:function(as){
                if(as.status == true){
                    alert(as.message);
                    location.reload();
                }
                else if(as.status == false){
                    alert(as.message);
                }
            }
        });
    }

    function error() {
        alert('Unable to retrieve your location');
    }

    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
    } else {
        navigator.geolocation.getCurrentPosition(success, error, options);
    }

}
    //Function to update the status
    $('.btn-accept').click(function(){
        geoFindMe();
    });
</script>
