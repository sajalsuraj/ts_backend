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
.btn{
    color: #fff !important;
}
</style>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDfncHjZ0r15lr_9BOYRg6jAlJ4JO5XQRA&libraries=places&callback=initAutocomplete" async defer></script>
<?php 
    $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
    $worker = $this->user->getProfileData($id); ?>
    <?php $services = $this->admin->getServicesLevelWise("3"); $serviceSelected = explode(",",$worker->sub_profession); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Partners</a>
    </li>
    <li class="breadcrumb-item active">Edit / <?php echo $worker->name; ?></li>
</ol>

<div class="row">
    <div class="col-md-12">
        <form id="addWorker">
            <div class="form-group">
                <label>Partner Name:</label>
                <input type="text" value="<?php echo $worker->name; ?>" class="form-control" required placeholder="Enter name" name="name" />
            </div>
            <div class="form-group">
                <label>City:</label>
                <input type="text" value="<?php echo $worker->city; ?>" class="form-control" required placeholder="Enter city" name="city" />
            </div>
            <div class="form-group">
                <label>Work location:</label>
                <input type="text" value="<?php echo $worker->work_location; ?>" class="form-control" id="location" required placeholder="Work location" name="work_location" />
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="number" value="<?php echo $worker->phone; ?>" class="form-control" required placeholder="Enter phone no." name="phone" />
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo $worker->email; ?>" class="form-control" required placeholder="Enter email" name="email" />
            </div>
            <div class="form-group">
                <label>Face photo:</label>
                <input type="file" name="face_photo" />
            </div>
            <div class="form-group">
                <label>Side face photo:</label>
                <input type="file" name="side_face_photo" />
            </div>
            <div class="form-group">
                <label>Full body photo:</label>
                <input type="file" name="full_body_photo" />
            </div>
            <div class="form-group">
                <label>Tool photo:</label>
                <input type="file" name="tool_photo" />
            </div>
            <div class="form-group">
                <label>Services:</label>
                <select class="form-control" name="sub_profession[]" id="profession" multiple="multiple">
                    <?php
                    foreach($services['result'] as $service){?>
                        <option <?php echo (in_array($service->id, $serviceSelected)?"selected":"");  ?> value="<?php echo $service->id; ?>"><?php echo $service->service_name; ?></option>
                    <?php } ?>
                    
                </select>
            </div>
            <div class="form-group">
                <label>Mode of transport:</label>
                <select name="mode_of_transport" class="form-control">
                    <?php $allVehicles = $this->admin->getAllVehicles(); ?>
                    <?php foreach ($allVehicles['result'] as $vehicle) { ?>
                        <option <?php if($worker->mode_of_transport === $vehicle->vehicle_name){echo "selected";} ?> value="<?php echo $vehicle->vehicle_name; ?>"><?php echo $vehicle->vehicle_name; ?></option> 
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Verification status:</label>
                <select class="form-control" name="otp_verified">
                    <option <?php if($worker->otp_verified == "1"){echo "selected";} ?> value="1">Verified</option>
                    <option <?php if($worker->otp_verified == "0"){echo "selected";} ?> value="0">Not Verified</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>

var lat = "<?php echo $worker->lat; ?>",
        lng = "<?php echo $worker->lng; ?>";
   
    $('#profession').select2();

    function initAutocomplete() {
        var input = document.getElementById("location");
        var searchBox = new google.maps.places.Autocomplete(input);
        searchBox.setComponentRestrictions({
            'country': ['in']
        });

        searchBox.addListener("place_changed", function() {
            var places = searchBox.getPlace();
            if (places.length == 0) {
                return;
            }

            lat = places.geometry.location.lat(),
                lng = places.geometry.location.lng()

        });
    }
    $("#addWorker").submit(function(event) {
        event.preventDefault();
    }).validate({
        rules: {},
        submitHandler: function(form) {

            var formData = new FormData(form);
            formData.append('user_id', <?php echo $worker->id; ?>);

            formData.append("lat",lat);
            formData.append("lng",lng);

            $.ajax({
                url: '<?php echo base_url(); ?>update/userprofile',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(as) {
                    if (as.status == true) {
                        alert(as.message);
                    } else if (as.status == false) {
                        alert(as.message);
                    }
                }
            });

        }
    });
</script>