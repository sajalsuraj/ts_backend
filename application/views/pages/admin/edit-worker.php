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
    $worker = $this->user->getProfileData($id); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Workers</a>
    </li>
    <li class="breadcrumb-item active">Edit / <?php echo $worker->name; ?></li>
</ol>

<div class="row">
    <div class="col-md-12">
        <form id="addWorker">
            <div class="form-group">
                <label>Worker Name:</label>
                <input type="text" value="<?php echo $worker->name; ?>" class="form-control" required placeholder="Enter name" name="name" />
            </div>
            <div class="form-group">
                <label>City:</label>
                <input type="text" value="<?php echo $worker->city; ?>" class="form-control" required placeholder="Enter city" name="city" />
            </div>
            <div class="form-group">
                <label>Work location:</label>
                <input type="text" value="<?php echo $worker->work_location; ?>" class="form-control" required placeholder="Work location" name="work_location" />
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="number" value="<?php echo $worker->phone; ?>" class="form-control" required placeholder="Enter phone no." name="phone" />
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo $worker->email; ?>" class="form-control" required placeholder="Enter email" name="email" />
            </div>
            <div class="form-group">
                <label>Face photo:</label>
                <input type="file" name="face_photo" />
            </div>
            <div class="form-group">
                <label>Side face photo:</label>
                <input type="file" name="side_face_photo" />
            </div>
            <div class="form-group">
                <label>Full body photo:</label>
                <input type="file" name="full_body_photo" />
            </div>
            <div class="form-group">
                <label>Tool photo:</label>
                <input type="file" name="tool_photo" />
            </div>
            <div class="form-group">
                <label>Mode of transport:</label>
                <select name="mode_of_transport" class="form-control">
                    <option <?php if($worker->mode_of_transport === "Bike"){echo "selected";} ?> value="Bike">Bike</option>
                    <option <?php if($worker->mode_of_transport === "Car"){echo "selected";} ?> value="Car">Car - Omni van</option>
                    <option <?php if($worker->mode_of_transport === "Public Transport"){echo "selected";} ?> value="Public Transport">Public transport</option>
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
        rules: {},
        submitHandler: function(form) {

            var formData = new FormData(form);
            formData.append('user_id', <?php echo $worker->id; ?>);

            $.ajax({
                url: '<?php echo base_url(); ?>update/userprofile',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(as) {
                    if (as.status == true) {
                        alert(as.message);
                    } else if (as.status == false) {
                        alert(as.message);
                    }
                }
            });

        }
    });
</script>