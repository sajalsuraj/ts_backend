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
label{
    font-weight:bold;
}
.red-font{
    color: red;
}
.green-font{
    color:green;
}
</style>
<?php $terms = $this->admin->getHomePage(); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">App</a>
    </li>
    <li class="breadcrumb-item active">Home Content</li>
</ol>
<div class="row">
    <div class="col-md-12">
        <form id="addParagraph">

            <div class="form-group">
                <label for="">Upper Content Heading:- </label>
                <input type="text" class="form-control" placeholder="Enter upper content heading" value="<?php if($terms != NULL){echo $terms->upper_content_heading;} ?>" name="upper_content_heading" />
            </div>

            <div class="form-group">
                <label for="">Upper Content Subheading:- </label>
                <textarea class="form-control" placeholder="Enter upper content subheading" name="upper_content_subheading"><?php if($terms != NULL){echo $terms->upper_content_subheading;} ?></textarea>
            </div>

            <div class="form-group">
                <label for="">Lower Content :- </label>
                <textarea class="form-control" placeholder="Enter lower content" name="lower_content"><?php if($terms != NULL){echo $terms->lower_content;} ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function(){
    var isFirstTime = 0;
    $("#addParagraph").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {
            },
        submitHandler: function(form) {
            var fd = new FormData(form);
            <?php $url = ""; if($terms != NULL){ $url = "update/homepage"; }else{ $url = "add/homepage"; } ?>
            var url = "<?php echo base_url().$url; ?>";
            <?php if($terms != NULL){ ?>
                isFirstTime = 1;
            <?php } ?>
            isFirstTime++;
            if(isFirstTime > 1){
                url = "<?php echo base_url(); ?>update/homepage";
                fd.append('id', '<?php echo $terms->id; ?>');
            }
            
            $.ajax({
                url: url,
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