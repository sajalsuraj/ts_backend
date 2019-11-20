<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin" || $this->session->userdata('type') == "worker") {
        if($this->session->userdata('type') == "admin"){
            $usertype = true;
        }
        else{
            $usertype = false;
        }
    } else {
        redirect('users/login');
    }
} else {
    redirect('users/login');
}
?>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Profile</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
</ol>

<?php $user = $this->user->getProfileData($this->session->userdata('user_id')); ?>

<div class="row">
    <div class="col-md-12">
        <form id="updateProfile">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Name:</td>
                        <td><?php echo $user->name; ?></td>
                    </tr>
                    <tr>
                        <td>Phone No:</td>
                        <td><?php echo $user->phone; ?></td>
                    </tr>
                    <tr>
                        <td>Email ID:</td>
                        <td><?php echo $user->email; ?></td>
                    </tr>
                    <?php if(!$usertype){ ?>
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
                            <option <?php if($user->primary_profession == $service->service_name){ echo "selected"; }  ?> value="<?php echo $service->service_name; ?>"><?php echo $service->service_name; ?></option>
                        <?php } ?>
                        </select>
                    </tr>
                    <tr>
                        <td>Mode of transportation:</td>
                        <td>
                            <?php $transport = ["Public Transport", "Car", "Bike"]; ?>
                            <select class="form-control" name="mode_of_transport">
                                <?php for($i = 0; $i < sizeof($transport); $i++){ ?>
                                    <option <?php if($user->mode_of_transport == $transport[$i]){ echo "selected"; }  ?> value="<?php echo $transport[$i]; ?>"><?php echo $transport[$i]; ?></option>
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
</div>
<script>
$("#updateProfile").submit(function(event) { 
    event.preventDefault();
}).validate({
    rules: {
    },
    submitHandler: function(form) {

        var formData = new FormData(form);
        formData.append('user_id', <?php echo $this->session->userdata('user_id'); ?>);
        
        $.ajax({
            url:'<?php echo base_url(); ?>update/profile',
            type: 'POST',
            data: formData,
            dataType:'json',
            processData: false,
            contentType: false,
            success:function(as){
                if(as.status == true){
                    alert(as.message);
                    location.href="profile";
                }
                else if(as.status == false){
                    alert(as.message);
                }
            }
        });
        
    }
});
</script>