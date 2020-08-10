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
    redirect('users/kycform-1');
}
else{
    $steps_filled = $this->admin->checkKYCStepsById($this->session->userdata('user_id'), 'kyc');            
    $count = (int) $steps_filled->steps_filled;

    if($count == 1){
        
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
$indianStates = ['AP' => 'Andhra Pradesh',
'AR' => 'Arunachal Pradesh',
'AS' => 'Assam',
'BR' => 'Bihar',
'CT' => 'Chhattisgarh',
'GA' => 'Goa',
'GJ' => 'Gujarat',
'HR' => 'Haryana',
'HP' => 'Himachal Pradesh',
'JK' => 'Jammu and Kashmir',
'JH' => 'Jharkhand',
'KA' => 'Karnataka',
'KL' => 'Kerala',
'LA' => 'Ladakh',
'MP' => 'Madhya Pradesh',
'MH' => 'Maharashtra',
'MN' => 'Manipur',
'ML' => 'Meghalaya',
'MZ' => 'Mizoram',
'NL' => 'Nagaland',
'OR' => 'Odisha',
'PB' => 'Punjab',
'RJ' => 'Rajasthan',
'SK' => 'Sikkim',
'TN' => 'Tamil Nadu',
'TG' => 'Telangana',
'TR' => 'Tripura',
'UP' => 'Uttar Pradesh',
'UT' => 'Uttarakhand',
'WB' => 'West Bengal',
'AN' => 'Andaman and Nicobar Islands',
'CH' => 'Chandigarh',
'DN' => 'Dadra and Nagar Haveli',
'DD' => 'Daman and Diu',
'LD' => 'Lakshadweep',
'DL' => 'New Delhi',
'PY' => 'Puducherry'];
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
        <h4>Personal Details</h4>
        <form id="kycform">
            <div class="form-group">
                <label>Father's name/Mother's Name</label>
                <input type="text" class="form-control" name="parent_name" />
            </div>

            <div class="form-group">
                <label>Gender</label>
                <select class="form-control" name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="form-group">
                <label>Date of birth</label>
                <input type="date" class="form-control" name="dob" />
            </div>

            <h4>Permanent Address</h4>
            <br>
            <div class="form-group">
                <label>House No./Flat No.</label>
                <input type="text" class="form-control" name="p_house_no" />
            </div>

            <div class="form-group">
                <label>Street/Locality/Colony</label>
                <input type="text" class="form-control" name="p_street" />
            </div>

            <div class="form-group">
                <label>Pincode</label>
                <input type="text" class="form-control" name="p_pincode" />
            </div>

            <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="p_city" />
            </div>

            <div class="form-group">
                <label>State</label>
                <select class="form-control" name="p_state">
                    <?php foreach($indianStates as $ind){ ?>
                        <option value="<?php echo $ind; ?>"><?php echo $ind; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    $("#kycform").submit(function(event) {
        event.preventDefault();
    }).validate({
        rules: {
            parent_name: "required",
            p_city: "required",
            p_state: "required",
            p_pincode: "required"
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
                        location.href="kycform-3";
                    }
                    else if(as.status == false){
                        alert(as.message);
                    }
                }
            });
        }
    });
</script>