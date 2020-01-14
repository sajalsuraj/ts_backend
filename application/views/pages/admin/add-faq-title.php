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
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">FAQ</a>
    </li>
    <li class="breadcrumb-item active">Add a title</li>
</ol>
<div class="row">
    <div class="col-md-12">
        <form id="addTitle">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" class="form-control" required placeholder="Add a title" name="title" />
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
$("#addTitle").submit(function(event) { 
    event.preventDefault();
}).validate({
    rules: {

    },
    submitHandler: function(form) {
        $.ajax({
            url:'<?php echo base_url(); ?>add/faqtitle',
            type: 'POST',
            data: new FormData(form),
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