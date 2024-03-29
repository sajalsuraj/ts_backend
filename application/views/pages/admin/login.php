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
        <div class="card-header">Login</div>
        <div class="card-body">
        <form id="adminLogin">
            <div class="form-group">
                <label for="inputEmail">Username</label>
                <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email address" required="required" autofocus="autofocus">
            </div>
            <div class="form-group">
                <label for="inputPassword">Password</label>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required="required">
            </div>
            <!-- <div class="form-group">
                <div class="checkbox">
                    <label>
                    <input type="checkbox" value="remember-me">
                    Remember Password
                    </label>
                </div>
            </div> -->
            <button class="btn btn-primary btn-block" type="submit">Login</button>
            <a class="forgot-pass" href="<?php echo base_url(); ?>users/forgot-password">Forgot Password?</a>
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
	     username: "required",
	     password: "required"
	    },
	    submitHandler: function(form) { 
	    	
	        $.ajax({
	        	url:'<?php echo base_url(); ?>get/adminLogin',
	        	type: 'POST',
                data: $('form').serialize(),
                dataType:'json',
                success:function(as){
                	if(as.status == true){
                		location.href="dashboard";
                	}
                	else if(as.status == false){
                		alert("Wrong Email or Password");
                	}
                }
	        });
	    }
	});
}
</script>
