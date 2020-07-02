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

<?php $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1); $user = $this->admin->getBankDetailsById($id, 'bank_details'); ?>

<div class="row">
    <div class="col-md-12">
        <form id="editForm">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Bank Name:</td>
                        <td><input type="text" name="bank_name" value="<?php if($user)echo $user->bank_name; ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Bank IFSC Code:</td>
                        <td><input type="text" name="ifsc_code" value="<?php if($user)echo $user->ifsc_code; ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Bank Account No:</td>
                        <td><input type="number" name="ac_no" value="<?php if($user)echo $user->ac_no; ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>Bank Cheque:</td>
                        <td><input type="file" name="bank_cheque" /></td>
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
        bank_name:{
            required:true
        },
        ifsc_code:{
            required:true
        },
        ac_no:{
            required:true
        }
    },
    submitHandler: function(form) {

        var formData = new FormData(form);
        formData.append('user_id', <?php echo $id; ?>);
        
        $.ajax({
            url:'<?php echo base_url(); ?>update/userbankdetails',
            type: 'POST',
            data: formData,
            dataType:'json',
            processData: false,
            contentType: false,
            success:function(as){
                if(as.status == true){
                    alert(as.message);
                    location.href="<?php echo base_url(); ?>/users/all-bank-details";
                }
                else if(as.status == false){
                    alert(as.message);
                }
            }
        });
        
    }
});
</script>

