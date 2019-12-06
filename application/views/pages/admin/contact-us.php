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
form{
    width:50%;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Contact Us</a>
    </li>
</ol>
<?php $contact = $this->admin->getContact(); ?>
<div class="row">
    <div class="col-md-12">
        <form id="addContact">
            <div class="form-group">
                <label>Email ID</label>
                <input type="text" value="<?php if($contact != NULL){echo $contact->email;} ?>" class="form-control" name="email" />
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="number" value="<?php if($contact != NULL){echo $contact->phone;} ?>" minlength="10" maxlength="10" class="form-control" name="phone" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function(){
    var hasSubmitted = 0;
    $("#addContact").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
        submitHandler: function(form) {
            
            var url = 'add/contact';
            var fd = new FormData(form);
            <?php if($contact != NULL){ ?>
                hasSubmitted = 1;
                url = 'update/contact';
                fd.append('id', '<?php echo $contact->id; ?>');
            <?php } ?>
            hasSubmitted++;
            if(hasSubmitted > 1){
                url = 'update/contact';
            }

            $.ajax({
                url:'<?php echo base_url(); ?>'+url,
                type: 'POST',
                data: fd,
                dataType:'json',
                processData: false,
                contentType: false,
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                    }
                    else if(as.status == false){
                        alert(as.message);
                    }
                }
            });

        }
    });

});
</script>