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
#addContent{
    width:50%;
}
.red-font{
    color: red;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo base_url(); ?>users/all-faq">FAQ</a>
    </li>
    <li class="breadcrumb-item active">Edit content</li>
</ol>
<?php $faqTitles = $this->admin->getAllFAQTitle();  ?>
<?php $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
$faq1 = $this->admin->faqById($id); ?>
<div class="row">
    <div class="col-md-12">
        <?php if(count($faqTitles['result']) > 0){ ?>
            <form id="addContent">
                <div class="form-group">
                    <label>Select FAQ Title:</label>
                    <select class="form-control" name="faq_title">
                        <?php 
                        foreach($faqTitles['result'] as $faq){ ?>
                        <option <?php if($faq->id === $faq1->faq_title){echo "selected";} ?> value="<?php echo $faq->id; ?>"><?php echo $faq->title; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Add FAQ Question:</label>
                    <input class="form-control" value="<?php echo $faq1->faq_category; ?>" type="text" name="faq_category" placeholder="Create a faq question" />
                </div>

                <div class="form-group">
                    <label>Add FAQ Answer:</label>
                    <textarea class="form-control" name="faq_description" placeholder="Add an answer"><?php echo $faq1->faq_description; ?></textarea>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>

            </form>
        <?php }else{ ?>
            <b class="red-font">FAQ titles are not available, please create FAQ titles.</b> <a href="add-faq-title">Click here</a> to create one.
        <?php } ?>
    </div>
</div>

<script>
$("#addContent").submit(function(event) { 
    event.preventDefault();
}).validate({
    rules: {
        faq_category:{
            required:true
        },
        faq_description:{
            required:true
        }
    },
    submitHandler: function(form) {
        var fD = new FormData(form);
        fD.append('id', '<?php echo $id; ?>');
        $.ajax({
            url:'<?php echo base_url(); ?>update/faq',
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