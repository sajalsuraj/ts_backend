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
textarea{
    height: 300px !important;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Disclaimer</a>
    </li>
</ol>
<?php $terms = $this->admin->getTerms("disclaimer"); ?>
<div class="row">
    <div class="col-md-12">
        <form id="addParagraph">
            <div class="form-group">
                <textarea id="terms" class="form-control" name="paragraph"><?php if($terms != NULL){echo $terms->paragraph;} ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#terms').richText();

    $("#addParagraph").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {
            },
        submitHandler: function(form) {
            var fd = new FormData(form);
            fd.append("type", "disclaimer");
            <?php $url = ""; if($terms != NULL){ $url = "update/static"; }else{ $url = "add/static"; } ?>
            $.ajax({
            url:'<?php echo base_url().$url; ?>',
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