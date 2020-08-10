<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin" || $this->session->userdata('type') == "worker") {
        if ($this->session->userdata('type') == "admin" || $this->session->userdata('type') == "superadmin") {
            $usertype = true;
        } else {
            $usertype = false;
        }
    } else {
        redirect('users/login');
    }
} else {
    redirect('users/login');
}
?>

<style>
.city-row{
    margin-bottom: 50px;
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

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Profile</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
</ol>

<?php $user = $this->user->getProfileData($this->session->userdata('user_id')); $serviceSelected = explode(",",$user->sub_profession); ?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#passwordChangeModal">Update Password</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <form id="updateProfile">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Name:</td>
                        <td> <input name="name" required class="form-control" value="<?php echo $user->name; ?>" required type="text"></td>
                    </tr>
                    <tr>
                        <td>Phone No:</td>
                        <td> <input name="phone" required maxlength="10" minlength="10" required class="form-control" value="<?php echo $user->phone; ?>" type="number"></td>
                    </tr>
                    <tr>
                        <td>Email ID:</td>
                        <td><input name="email" required required class="form-control" value="<?php echo $user->email; ?>" type="email"></td>
                    </tr>
                    <?php if (!$usertype) { ?>
                        <tr> 
                            <td>Face Image:</td>
                            <td><img style="width: 150px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->face_photo; ?>" /><input type="file" name="face_photo" /></td>
                        </tr>
                        <tr>
                            <td>Side Face Image:</td>
                            <td><img style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->side_face_photo; ?>" /><input type="file" name="side_face_photo" /></td>
                        </tr>
                        <tr>
                            <td>Full body Image:</td>
                            <td><img style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->full_body_photo; ?>" /><input type="file" name="full_body_photo" /></td>
                        </tr>
                        <tr>
                            <td>Tool Image:</td>
                            <td><img style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->tool_photo; ?>" /><input type="file" name="tool_photo" /></td>
                        </tr>
                        <tr>
                            <td>Work Location:</td>
                            <td><input id="location" type="text" class="form-control" name="work_location" value="<?php echo $user->work_location; ?>" /></td>
                        </tr>
                        <tr>
                            <td>Profession:</td>
                            <td>
                                <?php $allServices = $this->admin->getCategoriesWithSubcategories(); ?>
                                <select id="profession" multiple class="form-control" name="sub_profession[]">
                                <?php
                                    foreach($allServices as $category){?>
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
                        </tr>
                        <tr>
                            <td>Mode of transportation:</td>
                            <td>
                                <?php $transport = $this->admin->getAllVehicles(); ?>
                                <select class="form-control" name="mode_of_transport">
                                    <?php foreach($transport['result'] as $vehicle) { ?>
                                        <option <?php if ($user->mode_of_transport == $vehicle->vehicle_name) {
                                                    echo "selected";
                                                }  ?> value="<?php echo $vehicle->vehicle_name; ?>"><?php echo $vehicle->vehicle_name; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td><button class="btn btn-primary" type="submit">Submit</button></td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>

    <div id="passwordChangeModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update password</h5>
                    <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Old Password:</label>
                            <input id="oldPassword" class="form-control" placeholder="Enter old password" type="password" />
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail">New Password:</label>
                            <input id="newPassword" class="form-control" placeholder="Enter new password" type="password" />
                            <span id="err-msg"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="changePassword" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" onclick="$('#err-msg').html('');" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#profession').select2();

    var lat = "<?php echo $user->lat; ?>",
        lng = "<?php echo $user->lng; ?>";

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
    $("#updateProfile").submit(function(event) {
        event.preventDefault();
    }).validate({
        rules: {

        },
        submitHandler: function(form) {

            var formData = new FormData(form);
            formData.append('user_id', <?php echo $this->session->userdata('user_id'); ?>);
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
                        location.href = "profile";
                    } else if (as.status == false) {
                        alert(as.message);
                    }
                }
            });

        }
    });

    $('#changePassword').click(function(){
        if($('#oldPassword').val() == "" || $('#newPassword').val() == ""){
            alert('None of the fields can be empty');
        }
        else{
            var pattern = /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-zd$@$!%*?&].{8,}/;
            var tst = pattern.test($('#newPassword').val());
            if(tst){
                let formData = new FormData();
                formData.append('id', "<?php echo $this->session->userdata('user_id'); ?>");
                formData.append('old_password', $('#oldPassword').val());
                formData.append('new_password', $('#newPassword').val());
                $.ajax({
                    url: '<?php echo base_url(); ?>update/adminpassword',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(as) {
                        if (as.status == true) {
                            alert(as.message);
                            location.reload();
                        } else if (as.status == false) {
                            alert(as.message);
                        }
                    }
                });
            } 
            else{
                alert('Password should contain uppercase letter, symbols, and numbers. Should be of minimum 8 characters');
            }
        }
    });
</script>