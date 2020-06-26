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
<style>
@media only screen and (min-width: 900px){
    .rate{
        display:none;
    }
    #parent{
        width: 50%;
    }
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Partner</a>
    </li>
    <li class="breadcrumb-item active">Add a new one</li>
</ol>
<!--Add testimonial form starts -->
<div class="row">
    <div class="col-md-12">
        <form id="addBanners">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" placeholder="Enter name" class="form-control" name="name" />
            </div>
            <div class="form-group">
                <label>Upload image:</label>
                <input type="file" name="image">
            </div>
            <div class="form-group">
                <label>Show in homepage:</label>
                <input type="checkbox" name="show_in_homepage">
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
    window.onload = function(){

    
        $("#addBanners").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {
                
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url:'<?php echo base_url(); ?>add/partner',
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