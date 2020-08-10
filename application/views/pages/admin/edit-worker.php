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
.select2-results__group{
    color: #3b2d76;
    
}
.select2-results__option[aria-disabled=true]{
    color: #6954e1 !important;
    font-size: 1.1rem;
    font-weight: bold;
}
.select2-results__option[aria-selected=true], .select2-results__option[aria-selected=false]{
    color: #000 !important;
    font-size: 1.2rem;
    padding-left: 1.5em !important;
}

</style>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDfncHjZ0r15lr_9BOYRg6jAlJ4JO5XQRA&libraries=places&callback=initAutocomplete" async defer></script>
<?php 
    $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
    $worker = $this->user->getProfileData($id); ?>
    <?php $services = $this->admin->getCategoriesWithSubcategories(); $serviceSelected = explode(",",$worker->sub_profession); $cities = $this->admin->getAllCities(); ?>
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
                <select class="form-control" name="city">
                    <?php foreach($cities['result'] as $city){ ?>
                        <option <?php if($city->name == $worker->city){echo "selected";} ?> value="<?php echo $city->name; ?>"><?php echo $city->name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Work location:</label>
                <input type="text" value="<?php echo $worker->work_location; ?>" class="form-control" id="location" required placeholder="Work location" name="work_location" />
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="number" value="<?php echo $worker->phone; ?>" maxlength="10" minlength="10" class="form-control" required placeholder="Enter phone no." name="phone" />
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
                        foreach($services as $category){?>
                            <optgroup label="<?php echo $category->service_name; ?>">
                                <?php foreach($category->subcategories as $subcat){?>
                                    <option disabled><?php echo $subcat->service_name; ?></option>
                                    <?php foreach($subcat->subcategories as $maincat){  ?>
                                        <option <?php echo (in_array($maincat->id, $serviceSelected)?"selected":""); ?> class="level3-cat" value="<?php echo $maincat->id; ?>"><?php echo $maincat->service_name; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </optgroup>
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

    $('select[name=city]').select2();

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
        rules: {
            password:{
                pattern:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-zd$@$!%*?&].{8,}/
            }
        },
        messages:{
            password:{
                pattern: "Password should contain uppercase letter, symbols, and numbers. Should be of minimum 8 characters"
            }
        },
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