<?php
  if($this->session->has_userdata('type') == true){
    if($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin"){

    }
    else{
      redirect('users/login'); 
    }
  }
  else{
    redirect('users/login');
  }
?> 
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Services</a>
    </li>
    <li class="breadcrumb-item active">Add a new one</li>
</ol>

<!--Add testimonial form starts -->
<div class="row">
    <div class="col-md-12">
        <form id="addServices">
            <div class="form-group">
                <label>Service Name:</label>
                <input type="text" class="form-control" placeholder="Plumber, Carpenter, etc" name="service_name" />
            </div>
            <div class="form-group">
                <label>Rate per minute:</label>
                <input type="text" class="form-control" placeholder="30, 40, 50, etc" name="rate_per_min" />
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
    window.onload = function(){
        $("#addServices").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {
                name:{
                    required:true
                },
                rate_per_min:{
                    required:true
                }
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url:'<?php echo base_url(); ?>add/service',
                    type: 'POST',
                    data: new FormData(form),
                    dataType:'json',
                    processData: false,
                    contentType: false,
                    success:function(as){
                        if(as.status == true){
                            alert(as.message);
                            location.reload();
                        }
                        else if(as.status == false){
                            alert(as.message);
                        }
                    }
                });
                
            }
        });
    }
	
</script>