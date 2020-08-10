<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin" || $this->session->userdata('type') == "worker") {
        if($this->session->userdata('type') == "admin" || $this->session->userdata('type') == "superadmin"){
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
                    <td><img class="img-dash" style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->face_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Side Face Image:</td>
                    <td><img class="img-dash" style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->side_face_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Full body Image:</td>
                    <td><img class="img-dash" style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->full_body_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Tool Image:</td>
                    <td><img class="img-dash" style="width: 200px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $user->tool_photo; ?>" /></td>
                </tr>
                <tr>
                    <td>Work Location:</td>
                    <td><?php echo $user->work_location; ?></td>
                </tr>
                <tr>
                    <td>Primary Profession:</td>
                    <td>
                    <ul><?php $profs = explode(",",$user->sub_profession); foreach($profs as $p){ ?>
                        <li><?php echo isset($this->admin->getServiceById($p)->service_name)?$this->admin->getServiceById($p)->service_name:"NA"; } ?></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Mode of transportation:</td>
                    <td><?php echo $user->mode_of_transport; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div id="imageModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <img id="img-large" style="width:100%;" src="" alt="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.img-dash').click(function(){
        let imgSrc = $(this).attr('src');
        $('#img-large').attr('src',imgSrc);
        $('#imageModal').modal('show');
    });
</script>