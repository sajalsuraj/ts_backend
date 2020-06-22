<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin" || $this->session->userdata('type') == "worker") {
        if ($this->session->userdata('type') == "admin") {
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
</style>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Profile</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
</ol>

<?php $user = $this->user->getProfileData($this->session->userdata('user_id')); ?>
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
                        <td> <input name="name" required class="form-control" value="<?php echo $user->name; ?>" type="text"></td>
                    </tr>
                    <tr>
                        <td>Phone No:</td>
                        <td> <input name="phone" required class="form-control" value="<?php echo $user->phone; ?>" type="number"></td>
                    </tr>
                    <tr>
                        <td>Email ID:</td>
                        <td><input name="email" required class="form-control" value="<?php echo $user->email; ?>" type="email"></td>
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
                            <td><input type="text" class="form-control" name="work_location" value="<?php echo $user->work_location; ?>" /></td>
                        </tr>
                        <tr>
                            <td>Primary Profession:</td>
                            <td>
                                <?php $allServices = $this->admin->getAllServices(); ?>
                                <select class="form-control" name="primary_profession">
                                    <?php foreach ($allServices['result'] as $service) { ?>
                                        <option <?php if ($user->primary_profession == $service->service_name) {
                                                    echo "selected";
                                                }  ?> value="<?php echo $service->service_name; ?>"><?php echo $service->service_name; ?></option>
                                    <?php } ?>
                                </select>
                        </tr>
                        <tr>
                            <td>Mode of transportation:</td>
                            <td>
                                <?php $transport = ["Public Transport", "Car", "Bike"]; ?>
                                <select class="form-control" name="mode_of_transport">
                                    <?php for ($i = 0; $i < sizeof($transport); $i++) { ?>
                                        <option <?php if ($user->mode_of_transport == $transport[$i]) {
                                                    echo "selected";
                                                }  ?> value="<?php echo $transport[$i]; ?>"><?php echo $transport[$i]; ?></option>
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
    $("#updateProfile").submit(function(event) {
        event.preventDefault();
    }).validate({
        rules: {},
        submitHandler: function(form) {

            var formData = new FormData(form);
            formData.append('user_id', <?php echo $this->session->userdata('user_id'); ?>);

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
    });
</script>