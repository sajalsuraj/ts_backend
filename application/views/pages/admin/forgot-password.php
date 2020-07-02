<?php 
	
	if($this->session->has_userdata('type') == true){
		if($this->session->userdata('type') == "admin" || $this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "worker"){
			redirect('users/dashboard');
		}
	}

?>
<style>
    a.forgot-pass{
        float: right;
        margin-top: 10px;
        color: #6954e1;
    }
</style>
<div class="container">
    <div class="card card-login mx-auto mt-5">
        <div class="card-header">Forgot Password?</div>
        <div class="card-body">
        <form id="adminLogin">
            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email address" required="required" autofocus="autofocus">
            </div>

            <button class="btn btn-primary btn-block" type="submit">Submit</button>
            <a class="forgot-pass" href="<?php echo base_url(); ?>users/login">Back to login</a>
        </form>
        <!-- <div class="text-center">
            <a class="d-block small mt-3" href="register.html">Register an Account</a>
            <a class="d-block small" href="forgot-password.html">Forgot Password?</a>
        </div> -->
        </div>
    </div>
</div>
<script type="text/javascript">
window.onload = function(){
	$("#adminLogin").submit(function(event) {
	    event.preventDefault();
	}).validate({
	    rules: {
	     username: "required"
	    },
	    submitHandler: function(form) { 
	    	
	        $.ajax({
	        	url:'<?php echo base_url(); ?>get/adminemail',
	        	type: 'POST',
                data: $('form').serialize(),
                dataType:'json',
                success:function(as){
                	alert(as['message']);
                    location.reload();
                }
	        });
	    }
	});
}
</script>
