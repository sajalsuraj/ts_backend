<?php
  if($this->session->has_userdata('type') == true){
    if($this->session->userdata('type') == "superadmin"){

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
    .role-access{
        display:flex;
    }
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Admin</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
<?php $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1); $admin = $this->admin->getAdminProfile($id);
$roles = explode(",",$admin->roles);
?>
<!--Add testimonial form starts -->
<div class="row">
    <div class="col-md-12">
        <form id="addBanners">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" value="<?php echo $admin->name; ?>" required placeholder="Enter name" class="form-control" name="name" />
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="number" value="<?php echo $admin->phone; ?>" required placeholder="Enter phone" class="form-control" name="phone" />
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo $admin->email; ?>" required placeholder="Enter email" class="form-control" name="email" />
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" value="<?php echo $this->admin->crypt($admin->password, 'd'); ?>" required placeholder="Enter password" class="form-control" name="password" />
            </div>
            <div class="form-group role-access">
                <label>Page Access to:</label>
                <div class="col-md-4">
                    <input type="checkbox" <?php if(in_array("workers", $roles)){echo "checked";} ?> name="role[]" value="workers"> &nbsp; Workers <br>
                    <input type="checkbox" <?php if(in_array("workers_kyc", $roles)){echo "checked";} ?> name="role[]" value="workers_kyc"> &nbsp; Workers KYC <br>
                    <input type="checkbox" <?php if(in_array("app_homepage", $roles)){echo "checked";} ?> name="role[]" value="app_homepage"> &nbsp; App Homepage <br>
                    <input type="checkbox" <?php if(in_array("customers", $roles)){echo "checked";} ?> name="role[]" value="customers"> &nbsp; Customers <br>
                    <input type="checkbox" <?php if(in_array("service_requests", $roles)){echo "checked";} ?> name="role[]" value="service_requests"> &nbsp; Service Requests <br>
                    <input type="checkbox" <?php if(in_array("bookings", $roles)){echo "checked";} ?> name="role[]" value="bookings"> &nbsp; Bookings <br>
                    <input type="checkbox" <?php if(in_array("referrals", $roles)){echo "checked";} ?> name="role[]" value="referrals"> &nbsp; Referrals <br>
                    <input type="checkbox" <?php if(in_array("partners", $roles)){echo "checked";} ?> name="role[]" value="partners"> &nbsp; Partners <br>
                    <input type="checkbox" <?php if(in_array("contact_us", $roles)){echo "checked";} ?> name="role[]" value="contact_us"> &nbsp; Contact us 
                </div>

                <div class="col-md-4">
                    <input type="checkbox" <?php if(in_array("packages", $roles)){echo "checked";} ?> name="role[]" value="packages"> &nbsp; Packages <br>
                    <input type="checkbox" <?php if(in_array("memberships", $roles)){echo "checked";} ?> name="role[]" value="memberships"> &nbsp; Memberships <br>
                    <input type="checkbox" <?php if(in_array("services", $roles)){echo "checked";} ?> name="role[]" value="services"> &nbsp; Services <br>
                    <input type="checkbox" <?php if(in_array("faq", $roles)){echo "checked";} ?> name="role[]" value="faq"> &nbsp; FAQ <br>
                    <input type="checkbox" <?php if(in_array("cities", $roles)){echo "checked";} ?> name="role[]" value="cities"> &nbsp; Cities <br>
                    <input type="checkbox" <?php if(in_array("vehicles", $roles)){echo "checked";} ?> name="role[]" value="vehicles"> &nbsp; Vehicles <br>
                    <input type="checkbox" <?php if(in_array("notification", $roles)){echo "checked";} ?> name="role[]" value="notification"> &nbsp; Notifications <br>
                    <input type="checkbox" <?php if(in_array("static_pages", $roles)){echo "checked";} ?> name="role[]" value="static_pages"> &nbsp; Static Pages <br>
                    <input type="checkbox" <?php if(in_array("training", $roles)){echo "checked";} ?> name="role[]" value="training"> &nbsp; Training centers <br>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
    $("#addBanners").submit(function(event) { 
            event.preventDefault();
        }).validate({
        rules: {
            "role[]":{
                required: true,
            }
        },
        messages:{
            "role[]":"Atleast one option should be checked"
        },
        submitHandler: function(form) {
            var fD = new FormData(form);
            fD.append("id", "<?php echo $id; ?>");
            $.ajax({
                url:'<?php echo base_url(); ?>update/admin',
                type: 'POST',
                data: fD,
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
</script>