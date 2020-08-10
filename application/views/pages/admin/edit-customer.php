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
.table{
    font-size: 12px;
}
.btn{
    color: #fff !important;
}
</style>
<?php 
    $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
    $customer = $this->user->getCustomerData($id); ?>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo base_url(); ?>users/all-customers">Customer</a>
    </li>
    <li class="breadcrumb-item active">Edit / <?php echo $customer->name; ?></li>
</ol>

<div class="row">
    <div class="col-md-12">
        <form id="addWorker">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" value="<?php echo $customer->name; ?>" class="form-control" required placeholder="Enter name" name="name" />
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo $customer->email; ?>" class="form-control" required placeholder="Enter email" name="email" />
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="number" value="<?php echo $customer->phone; ?>" maxlength="10" minlength="10" class="form-control" required placeholder="Enter phone" name="phone" />
            </div>
            <div class="form-group">
                <label>Verification status:</label>
                <select class="form-control" name="otp_verified">
                    <option <?php if($customer->otp_verified == "1"){echo "selected";} ?> value="1">Verified</option>
                    <option <?php if($customer->otp_verified == "0"){echo "selected";} ?> value="0">Not Verified</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
    $("#addWorker").submit(function(event) {
        event.preventDefault();
    }).validate({
        rules: {
            phone:{
                required:true,
                minlength:10,
                maxlength:10,
                number: true
            }
        },
        submitHandler: function(form) {

            var formData = new FormData(form);
            formData.append('id', <?php echo $id; ?>);

            $.ajax({
                url: '<?php echo base_url(); ?>update/customer',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(as) {
                    if (as.status == true) {
                        alert(as.message);
                        location.reload();
                    } else if (as.status == false) {
                        alert(as.message);
                    }
                }
            });

        }
    });
</script>
