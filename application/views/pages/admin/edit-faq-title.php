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
        <a href="<?php echo base_url(); ?>users/all-faq-title">FAQ Title</a>
    </li>
    <li class="breadcrumb-item active">Edit title</li>
</ol>
<?php $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
$faq1 = $this->admin->faqTitleById($id); ?>
<div class="row">
    <div class="col-md-12">
        <form id="addTitle">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" value="<?php echo $faq1->title; ?>" class="form-control" required placeholder="Add a title" name="title" />
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
        var fD = new FormData(form);
        fD.append('id', '<?php echo $id; ?>');
        $.ajax({
            url:'<?php echo base_url(); ?>update/faqtitle',
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