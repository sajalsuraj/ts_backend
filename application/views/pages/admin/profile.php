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
<style>
a.btn{
    color: #fff !important;
}
</style>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">User</a>
    </li>
    <li class="breadcrumb-item active">Profile</li>
</ol>

<?php $user = $this->user->getProfileData($this->session->userdata('user_id')); ?>

<div class="row">
    <div class="col-md-12">
        <div class="col-md-12">
            <a href="edit-profile" class="btn btn-success float-right">Edit Profile</a>
        </div>
        <br>
        <br>
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
                    <td><img style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->face_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Side Face Image:</td>
                    <td><img style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->side_face_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Full body Image:</td>
                    <td><img style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->full_body_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Tool Image:</td>
                    <td><img style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->tool_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Work Location:</td>
                    <td><?php echo $user->work_location; ?></td>
                </tr>
                <tr>
                    <td>Primary Profession:</td>
                    <td><?php echo $user->primary_profession; ?></td>
                </tr>
                <tr>
                    <td>Mode of transportation:</td>
                    <td><?php echo $user->mode_of_transport; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>