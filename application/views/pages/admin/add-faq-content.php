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
        <a href="#">FAQ</a>
    </li>
    <li class="breadcrumb-item active">Add a content</li>
</ol>
<?php $faqTitles = $this->admin->getAllFAQTitle(); ?>
<div class="row">
    <div class="col-md-12">
        <?php if(count($faqTitles['result']) > 0){ ?>
            <form id="addContent">
                <div class="form-group">
                    <label>Select FAQ Title:</label>
                    <select class="form-control" name="faq_title">
                        <?php 
                        foreach($faqTitles['result'] as $faq){ ?>
                        <option value="<?php echo $faq->id; ?>"><?php echo $faq->title; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Add FAQ Question:</label>
                    <input class="form-control" type="text" name="faq_category" placeholder="Create a faq question" />
                </div>

                <div class="form-group">
                    <label>Add FAQ Answer:</label>
                    <textarea class="form-control" name="faq_description" placeholder="Add an answer"></textarea>
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
        $.ajax({
            url:'<?php echo base_url(); ?>add/faqcontent',
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