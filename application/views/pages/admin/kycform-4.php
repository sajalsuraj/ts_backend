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

<?php
if(!$this->admin->checkKYCById($this->session->userdata('user_id'), 'kyc')){
    redirect('users/kycform-1');
}
else{
    $steps_filled = $this->admin->checkKYCStepsById($this->session->userdata('user_id'), 'kyc');            
    $count = (int) $steps_filled->steps_filled;

    if($count == 1){
        redirect('users/kycform-2');
    }
    else if($count == 2){
        redirect('users/kycform-3');
    }
    else if($count == 3){
       
    }
    else if($count == 4){
        redirect('users/dashboard');
    }
}
?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
    <a href="#">User</a>
    </li>
    <li class="breadcrumb-item active">Add KYC Detail</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <form id="kycform">
            <div class="form-group">
                <input type="checkbox" name="declaration" /> I agree to all the terms and conditions
            </div>

            <div class="form-group">
                <button type="submit" disabled class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
$("input[name=declaration]").on('change', function() {
    if(this.checked) {
        $('button[type=submit]').attr('disabled', false);
    } else {
        $('button[type=submit]').attr('disabled', true);
    }
});

$("#kycform").submit(function(event) {
    event.preventDefault();
}).validate({
    rules: {
        parent_name: "required",
        p_city: "required",
        p_state: "required",
        p_pincode: "required"
    },
    submitHandler: function(form) { 

        var formData = new FormData(form);
        formData.append('user_id', <?php echo $this->session->userdata('user_id'); ?>);
        
        $.ajax({
            url:'<?php echo base_url(); ?>add/kycdetail',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType:'json',
            success:function(as){
                if(as.status == true){
                    alert(as.message);
                    location.href="dashboard";
                }
                else if(as.status == false){
                    alert(as.message);
                }
            }
        });
    }
});
</script>