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
   .f-holder{
       float:left;
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDfncHjZ0r15lr_9BOYRg6jAlJ4JO5XQRA&libraries=places&callback=initAutocomplete" async defer></script>
<?php $services = $this->admin->getCategoriesWithSubcategories(); $cities = $this->admin->getAllCities(); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Partner</a>
    </li>
    <li class="breadcrumb-item active">Add a new one</li>
</ol>
<div class="row">
    <div class="col-md-12">
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
                    <?php  ?>
                    <select required class="form-control" name="sub_profession[]" id="profession" multiple="multiple">
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
<script>
    var lat = "",
        lng = "";

        $('#profession').select2();

    function initAutocomplete() {
        var input = document.getElementById("location");
        var searchBox = new google.maps.places.Autocomplete(input);
        searchBox.setComponentRestrictions({
            'country': ['in']
        });

        searchBox.addListener("place_changed", function() {
            var places = searchBox.getPlace();
            if (places.length == 0) {
                return;
            }

            lat = places.geometry.location.lat(),
                lng = places.geometry.location.lng()

        });
    }

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