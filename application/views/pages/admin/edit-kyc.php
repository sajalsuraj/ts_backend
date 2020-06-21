<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin") {
    } else {
        redirect('users/login');
    }
} else {
    redirect('users/login');
}
?>
<style>
    .table {
        font-size: 12px;
    }

    .btn {
        color: #fff !important;
    }
</style>
<?php
$id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
$kyc = $this->admin->getKycByID($id); ?>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo base_url(); ?>users/all-customers">KYC</a>
    </li>
    <li class="breadcrumb-item active">Edit / <?php echo $kyc[0]->name; ?></li>
</ol>

<div class="row">
    <div class="col-md-12">
        <form id="addWorker">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" value="<?php echo $kyc[0]->name; ?>" class="form-control" required placeholder="Enter name" name="name" />
            </div>

            <div class="form-group">
                <label>ID Number:</label>
                <input type="text" value="<?php echo $kyc[0]->id_number; ?>" class="form-control" required placeholder="Enter ID number" name="id_number" />
            </div>
            <div class="form-group">
                <label>Image front side:</label>
                <input type="file" name="img_front_side" />
            </div>
            <div class="form-group">
                <label>Image back side:</label>
                <input type="file" name="img_back_side" />
            </div>

            <div class="form-group">
                <label>Parent name:</label>
                <input type="text" value="<?php echo $kyc[0]->parent_name; ?>" class="form-control" required placeholder="Enter parent name" name="parent_name" />
            </div>

            <div class="form-group">
                <label>Gender:</label>
                <select class="form-control" name="gender">
                    <option <?php if ($kyc[0]->gender == "Male") {
                                echo "selected";
                            } ?> value="Male">Male</option>
                    <option <?php if ($kyc[0]->gender == "Female") {
                                echo "selected";
                            } ?> value="Female">Female</option>
                </select>
            </div>

            <div class="form-group">
                <label>DOB:</label>
                <input type="text" value="<?php echo $kyc[0]->dob; ?>" class="form-control" required placeholder="Enter DOB" name="dob" />
            </div>
            <h5>Permanent Address -</h5>
            <div class="form-group">
                <label>House:</label>
                <input type="text" value="<?php echo $kyc[0]->p_house_no; ?>" class="form-control" required placeholder="Enter house no" name="p_house_no" />
            </div>
            <div class="form-group">
                <label>Street:</label>
                <input type="text" value="<?php echo $kyc[0]->p_street; ?>" class="form-control" required placeholder="Enter street" name="p_street" />
            </div>

            <div class="form-group">
                <label>Pincode:</label>
                <input type="number" value="<?php echo $kyc[0]->p_pincode; ?>" class="form-control" required placeholder="Enter pincode" name="p_pincode" />
            </div>

            <div class="form-group">
                <label>City:</label>
                <input type="text" value="<?php echo $kyc[0]->p_city; ?>" class="form-control" required placeholder="Enter city" name="p_city" />
            </div>

            <div class="form-group">
                <label>State:</label>
                <input type="text" value="<?php echo $kyc[0]->p_state; ?>" class="form-control" required placeholder="Enter state" name="p_state" />
            </div>

            <h5>Current Address -</h5>
            <div class="form-group">
                <label>House:</label>
                <input type="text" value="<?php echo $kyc[0]->c_house_no; ?>" class="form-control" required placeholder="Enter house no" name="c_house_no" />
            </div>
            <div class="form-group">
                <label>Street:</label>
                <input type="text" value="<?php echo $kyc[0]->c_street; ?>" class="form-control" required placeholder="Enter street" name="c_street" />
            </div>

            <div class="form-group">
                <label>Pincode:</label>
                <input type="number" value="<?php echo $kyc[0]->c_pincode; ?>" class="form-control" required placeholder="Enter pincode" name="c_pincode" />
            </div>

            <div class="form-group">
                <label>City:</label>
                <input type="text" value="<?php echo $kyc[0]->c_city; ?>" class="form-control" required placeholder="Enter city" name="c_city" />
            </div>

            <div class="form-group">
                <label>State:</label>
                <input type="text" value="<?php echo $kyc[0]->c_state; ?>" class="form-control" required placeholder="Enter state" name="c_state" />
            </div>

            <div class="form-group">
                <label>Verification status:</label>
                <select class="form-control" name="is_verified">
                    <option <?php if ($kyc[0]->is_verified == "0") {
                                echo "selected";
                            } ?> value="0">Not verified</option>
                    <option <?php if ($kyc[0]->is_verified == "1") {
                                echo "selected";
                            } ?> value="1">Verified</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
    $('input[name=dob]').datepicker({
        minDate: 0,
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        changeYear: true
    });

    $("#addWorker").submit(function(event) {
        event.preventDefault();
    }).validate({
        rules: {
           
        },
        submitHandler: function(form) {

            var formData = new FormData(form);
            formData.append('user_id', <?php echo $id; ?>);

            $.ajax({
                url: '<?php echo base_url(); ?>update/kycdata',
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