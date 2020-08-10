<style>
   .f-holder{
       float:left;
   }
   .error{
       color:red;
   }
   .select2-results__group{
        color: #3b2d76;
        
    }
    .select2-results__option[aria-disabled=true]{
        color: #6954e1 !important;
        font-size: 1.1rem;
        font-weight: bold;
    }
    .select2-results__option[aria-selected=true], .select2-results__option[aria-selected=false]{
        color: #000 !important;
        font-size: 1.2rem;
        padding-left: 1.5em !important;
    }
    .eye-icon{
        position: absolute;
        right: 0;
        margin-right: 25px;
        cursor: pointer;
        margin-top: -24px;
    }
</style>
<?php $services = $this->admin->getCategoriesWithSubcategories(); $cities = $this->admin->getAllCities(); ?>
<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal"></h5>
    <nav class="my-2 my-md-0 mr-md-3">
    <a class="p-2 text-dark" href="#">Help Center</a>
    <a class="p-2 text-dark" href="users/login">Login</a>
    </nav>
</div>
<div class="container">
    <h2 class="head-1"><span>Signup</span> as a partner on Troubleshooters</h2>

    <div class="col-md-10 offset-md-1">
    <form id="addVendors">
            <div class="col-md-6 f-holder">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" required placeholder="Enter name" class="form-control" name="name" />
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <select class="form-control" name="city">
                        <?php foreach($cities['result'] as $city){ ?>
                            <option value="<?php echo $city->name; ?>"><?php echo $city->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Your location:</label>
                    <input type="text" id="location" required placeholder="Enter your primary location" class="form-control" name="work_location" />
                </div>
                <div class="form-group">
                    <label>Select Profession:</label>
                    <select class="form-control" name="sub_profession[]" id="profession" multiple="multiple">
                        <?php
                            foreach($services as $category){?>
                                <optgroup label="<?php echo $category->service_name; ?>">
                                    <?php foreach($category->subcategories as $subcat){?>
                                        <option disabled><?php echo $subcat->service_name; ?></option>
                                        <?php foreach($subcat->subcategories as $maincat){  ?>
                                            <option class="level3-cat" value="<?php echo $maincat->id; ?>"><?php echo $maincat->service_name; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </optgroup>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="number" required placeholder="Enter phone number" class="form-control" name="phone" />
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" required placeholder="Enter email" class="form-control" name="email" />
                </div>

                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" required placeholder="Enter password" class="form-control" name="password" />
                    <i class="fas fa-eye-slash eye-icon"></i>
                </div>
            </div>

            <div class="col-md-6 f-holder">
                <div class="form-group">
                    <label>Face photo:</label>
                    <input type="file" class="form-control" required name="face_photo" />
                </div>

                <div class="form-group">
                    <label>Side face photo:</label>
                    <input type="file" class="form-control" required name="side_face_photo" />
                </div>

                <div class="form-group">
                    <label>Full body photo:</label>
                    <input type="file" class="form-control" required name="full_body_photo" />
                </div>

                <div class="form-group">
                    <label>Tool photo:</label>
                    <input type="file" class="form-control" required name="tool_photo" />
                </div>

                <div class="form-group">
                    <label>Mode of transport:</label>
                    <?php $allVehicles = $this->admin->getAllVehicles(); ?>
                    <select class="form-control" name="mode_of_transport">
                        <?php foreach ($allVehicles['result'] as $vehicle) { ?>
                            <option value="<?php echo $vehicle->vehicle_name; ?>"><?php echo $vehicle->vehicle_name; ?></option> 
                        <?php } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </div>
            
            
        </form>
    </div>
</div>
<!-- The Modal -->
<div class="modal" id="otpModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">OTP Verification</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="container-fluid">
            
                <div class="form-group">
                    <label>Enter the OTP which has been sent to your mobile:</label>
                    <input type="number" class="form-control" id="otp" placeholder="OTP">
                    <span id="err_msg"></span>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary btn-signup" id="verifyOTP" type="button">Verify</button>
                </div>
            
        </div>
      </div>

    </div>
  </div>
</div>

<script>

var lat = "",
        lng = "";
        $('#profession').select2();

        function initAutocomplete() {
            

            var input = document.getElementById("exampleInputEmail3");
            var searchBox = new google.maps.places.Autocomplete(input);
            searchBox.setComponentRestrictions(
                {'country': ['in']});

            searchBox.addListener("place_changed", function() {
                var places = searchBox.getPlace();
                if (places.length == 0) {
                    return;
                }

                lat = places.geometry.location.lat(),
                lng = places.geometry.location.lng()

            });

            
        }

   

        var ph_no = "";

        

        // $.fn.serializeObject = function()
        // {
        //     var o = {};
        //     var a = this.serializeArray();
        //     $.each(a, function() {
        //         if (o[this.name] !== undefined) {
        //             if (!o[this.name].push) {
        //                 o[this.name] = [o[this.name]];
        //             }
        //             o[this.name].push(this.value || '');
        //         } else {
        //             o[this.name] = this.value || '';
        //         }
        //     });
        //     return o;
        // };
        $("#addVendors").submit(function(event) {
            event.preventDefault();
        }).validate({
        rules: {
            password:{
                pattern:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-zd$@$!%*?&].{8,}/
            }
        },
        messages:{
            password:{
                pattern: "Password should contain uppercase letter, symbols, and numbers. Should be of minimum 8 characters"
            }
        },
        submitHandler: function(form) {

            var fD = new FormData(form);
            fD.append('lat', lat);
            fD.append('lng', lng);
            //fD.append('services', JSON.stringify(serArr));
            $.ajax({
                url: '<?php echo base_url(); ?>add/vendorsignup',
                type: 'POST',
                data: fD,
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

    $('#verifyOTP').click(function(){
        if($('#otp').val() == ""){
            $('#err_msg').html("Please enter the OTP");
        }
        else{
            $('#err_msg').val("");

            var pData = new FormData();
            pData.append('otp', $('#otp').val());
            pData.append('phone', ph_no);

            $.ajax({
                url:'<?php echo base_url(); ?>get/verifyotp',
                type: 'POST',
                data: pData,
                processData: false,
                contentType: false,
                dataType:'json',
                success:function(as){
                    console.log(as);
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
    
    $('.eye-icon').click(function(){
        if($('input[name=password]').attr('type') == 'password'){
            $('input[name=password]').attr('type','text');
            $(this).removeClass('fa-eye-slash');
            $(this).addClass('fa-eye');
        }
        else{
            $('input[name=password]').attr('type','password');
            $(this).removeClass('fa-eye');
            $(this).addClass('fa-eye-slash');
        }
    });

</script>