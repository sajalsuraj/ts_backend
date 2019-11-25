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
    
}
</style>
<!-- Breadcrumbs-->
<?php 
    $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
    $service =  $this->admin->getServiceById($id);

    $hasCharge = $this->admin->checkIfServiceHasCharge($id);
?>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo base_url() ?>users/all-services">Services</a>
    </li>
    <li class="breadcrumb-item active">Edit / <?php echo $service->service_name; ?></li>
</ol>

<!--Add testimonial form starts -->
<div class="row">
    <div class="col-md-12">
        <form id="addServices">
            <div class="form-group">
                <label>Service Name:</label>
                <input type="text" value="<?php echo $service->service_name; ?>" class="form-control" required placeholder="Plumber, Carpenter, etc" name="service_name" />
            </div>
            <?php if($hasCharge){ ?>
                <div class="form-group rate">
                    <label>Rate per minute:</label>
                    <input type="text" value="<?php echo $service->rate_per_min; ?>" class="form-control" placeholder="30, 40, 50, etc" name="rate_per_min" />
                </div>
                <div class="form-group rate">
                    <label>Details (Optional):</label>
                    <input type="text" value="<?php echo $service->detail; ?>" class="form-control" placeholder="Installation, Repair, Replace, etc." name="detail" />
                </div>
            <?php } ?>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Update</button>
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

            },
            submitHandler: function(form) {

                var fd = new FormData(form);
                fd.append('id', <?php echo $service->id; ?>);
                
                $.ajax({
                    url:'<?php echo base_url(); ?>update/service',
                    type: 'POST',
                    data: fd,
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