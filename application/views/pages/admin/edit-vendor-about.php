<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin") {

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
        <a href="#">Partner</a>
    </li>
    <li class="breadcrumb-item active">Edit About</li> 
</ol>

<?php $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1); $user = $this->admin->getUserAboutById($id, 'about'); ?>

<div class="row">
    <div class="col-md-12">
        <form id="editForm">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Year:</td>
                        <td><input type="number" maxlength="4" minlength="4" name="year" value="<?php if($user)echo $user->year; ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Month:</td>
                        <td><input type="number" maxlength="2" minlength="1" name="month" value="<?php if($user)echo $user->month; ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Business Name:</td>
                        <td><input type="text" name="business" value="<?php if($user)echo $user->business; ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Phone No.:</td>
                        <td><input type="number" maxlength="10" minlength="10" name="phone" value="<?php if($user)echo $user->phone; ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Website:</td>
                        <td><input type="text" name="website" value="<?php if($user)echo $user->website; ?>" placeholder="Enter with http:// or https://" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Introduction:</td>
                        <td><textarea class="form-control" name="intro"><?php if($user)echo $user->intro; ?></textarea></td>
                    </tr>
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
$("#editForm").submit(function(event) { 
    event.preventDefault();
}).validate({
    rules: {
        business:{
            required:true
        },
        intro:{
            required:true
        }
    },
    submitHandler: function(form) {

        var formData = new FormData(form);
        formData.append('user_id', <?php echo $id; ?>);
        
        $.ajax({
            url:'<?php echo base_url(); ?>update/userabout',
            type: 'POST',
            data: formData,
            dataType:'json',
            processData: false,
            contentType: false,
            success:function(as){
                if(as.status == true){
                    alert(as.message);
                    location.href="<?php echo base_url(); ?>/users/all-about";
                }
                else if(as.status == false){
                    alert(as.message);
                }
            }
        });
        
    }
});
</script>

