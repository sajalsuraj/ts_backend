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
        <form id="addMember">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">FULL NAME</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" name="name" placeholder="Full Name">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail2">YOUR CITY</label>
                        <input type="text" class="form-control" id="exampleInputEmail2" name="city" placeholder="Your City">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail3">ENTER YOUR WORK LOCATION</label>
                        <input type="text" class="form-control" id="exampleInputEmail3" name="work_location" placeholder="Pick your location">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail8">MODE OF TRANSPORTATION</label>
                        <select class="form-control" name="mode_of_transport">
                            <option value="Public Transport">Public Transport</option>
                            <option value="Car">Car</option>
                            <option value="Bike">Bike</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail4">YOUR PRIMARY PROFESSION</label>
                        <?php $allServices = $this->admin->getAllServices(); ?>
                        <select class="form-control" name="primary_profession">
                        <?php foreach ($allServices['result'] as $service) { ?>
                            <option value="<?php echo $service->service_name; ?>"><?php echo $service->service_name; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail5">PHONE NUMBER</label>
                        <input type="text" class="form-control" id="exampleInputEmail5" name="phone" placeholder="Phone Number">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail6">EMAIL</label>
                        <input type="text" class="form-control" id="exampleInputEmail6" name="email" placeholder="Email">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail7">PASSWORD</label>
                        <input type="password" class="form-control" id="exampleInputEmail7" name="password" placeholder="Password">
                    </div>
                </div>
            </div>

            <div class="row">
                <button class="btn btn-primary btn-signup" type="submit">Signup</button>
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

        

        $.fn.serializeObject = function()
        {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };
        $("#addMember").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {
                name:{
                    required: true
                },
                work_location:{
                    required: true
                },
                email:{
                    required: true
                },
                password:{
                    required:true
                },
                phone:{
                    required:true
                }
            },
            submitHandler: function(form) { 

                $('button[type=submit]').attr("disabled", true);
                var formData = $('form').serializeObject();
                formData['lat'] = lat;
                formData['lng'] = lng;
                $.ajax({
                    url:'<?php echo base_url(); ?>add/signup',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    processData: false,
                    contentType: false,
                    dataType:'json',
                    success:function(as){
                        $('button[type=submit]').attr("disabled", false);
                        if(as.status == true){
                            ph_no = as.phone;
                            $('#otpModal').modal('show');
                        }
                        else if(as.status == false){
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
    

</script>