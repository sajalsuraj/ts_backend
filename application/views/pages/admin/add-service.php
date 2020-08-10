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
        <a href="#">Services</a>
    </li>
    <li class="breadcrumb-item active">Add a new one</li>
</ol>
<?php 
    $parentCategories =  $this->admin->getServicesExceptLevel3();
?>
<!--Add testimonial form starts -->
<div class="row">
    <div class="col-md-12">
        <form id="addServices">
            <div class="form-group">
                <label>Category:</label>
                <select class="form-control" id="parent">
                    <option value="parent">Parent Category</option>
                    <?php 
                    foreach($parentCategories['result'] as $parent){
                    ?>
                    <option data-parent="<?php echo $parent->parent_category; ?>" value="<?php echo $parent->id; ?>"><?php echo $parent->service_name; ?></option>
                    <?php } ?>  
                </select>
            </div>
            <div class="form-group">
                <label>Service Name:</label>
                <input type="text" class="form-control" required placeholder="Plumber, Carpenter, etc" name="service_name" />
            </div>
            <div class="form-group">
                <label>Image:</label>
                <input type="file" name="image" />
            </div>
            <div class="form-group rate">
                <label>Rate:</label>
                <input type="text" class="form-control" placeholder="30, 40, 50, etc" name="rate_per_min" />
            </div>
            <div class="form-group rate">
                <label>Average Service time taken:</label>
                <input type="text" class="form-control" placeholder="Eg. 30 Min" name="avg_time_taken" />
            </div>
            <div class="form-group rate">
                <label>Rate mode:</label>
                <select class="form-control" name="mode">
                    <option value="rate_per_min">Rate per minute</option>
                    <option value="fixed">Fixed Rate</option>
                </select>
            </div>
            <div class="form-group rate">
                <label>Details (Optional):</label>
                <input type="text" class="form-control" placeholder="Installation, Repair, Replace, etc." name="detail" />
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
    window.onload = function(){

        $('#parent').change(function(){
            if($(this).val() === "parent"){
                $('.rate').hide();
            }
            else{
                if($(this).find(':selected').attr('data-parent') === ""){
                    $('.rate').hide();
                }
                else{
                    $('.rate').show();
                }
            }
        });

        $("#addServices").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {

            },
            submitHandler: function(form) {


                var formData = new FormData();
                if($('#parent').val() == "parent"){
                    formData.append('level', "1");
                }
                else{
                    if($('#parent').find(':selected').attr('data-parent') === ""){
                        formData.append('level', "2");
                    }
                    else{
                        if($('input[name="rate_per_min"]').val() == ""){
                            alert("Rate per minute is required");
                        return false;
                        }
                        formData.append('level', "3");
                        formData.append('mode', $('select[name="mode"]').val());
                    }
                    formData.append('parent_category', $('#parent').val());
                    formData.append('rate_per_min', $('input[name="rate_per_min"]').val());
                    formData.append('detail', $('input[name="detail"]').val());
                    formData.append('avg_time_taken', $('input[name="avg_time_taken"]').val());
                }
                formData.append('service_name', $('input[name="service_name"]').val());
                formData.append('image', $('input[type=file]')[0].files[0]);
                
                $.ajax({
                    url:'<?php echo base_url(); ?>add/service',
                    type: 'POST',
                    data: formData,
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