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
        redirect('users/kycform-4');
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
        <h4>What ID would you like to add?</h4>
        <form id="kycform">
            <div class="form-group">
                <label>ID Type</label>
                <select class="form-control" name="id_type">
                    <option value="PAN">PAN</option>
                    <option value="Voter ID">Voter ID</option>
                    <option value="Driving License">Driving License</option>
                    <option value="Aadhar">Aadhar</option>
                </select>
            </div>

            <div class="form-group">
                <label>Name on selected ID proof</label>
                <input type="text" class="form-control" name="name" />
            </div>

            <div class="form-group">
                <label id="id_number"></label>
                <input type="text" class="form-control" name="id_number" />
            </div>

            <div class="form-group">
                <label>Upload front side image</label>
                <input type="file" name="img_front_side" />
            </div>

            <div id="back_img" class="form-group">
                <label>Upload back side image</label>
                <input type="file" name="img_back_side" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
        </form>
    </div>
</div>
<script>
    if($('select[name=id_type]').val() == "PAN"){
        $('#back_img').hide();
    }
    else{
        $('#back_img').show();
    }
    $('#id_number').text($('select[name=id_type]').val()+" Number");
    $('select[name=id_type]').change(function(){
        $('#id_number').text($('select[name=id_type]').val()+" Number");
        if($('select[name=id_type]').val() == "PAN"){
            $('#back_img').hide();
        }
        else{
            $('#back_img').show();
        }
    });



	$("#kycform").submit(function(event) {
	    event.preventDefault();
	}).validate({
	    rules: {
	     name: "required",
	     id_number: "required",
         img_front_side: "required"
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
                        location.href="kycform-2";
                	}
                	else if(as.status == false){
                		alert(as.message);
                	}
                }
	        });
	    }
	});

</script>