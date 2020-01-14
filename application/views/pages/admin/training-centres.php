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
.city-row{
    margin-bottom: 50px;
}
.btn-del{
    color: #fff !important;
}
.video_thumb{
    width: 200px;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Training Videos</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $training = $this->admin->getAllTrainingVideos(); ?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#videoModal">Add a video</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>Heading</th>
                    <th>Thumbnail</th>
                    <th>Video File</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($training['result'] as $city) { ?>
                    <tr id="b_<?php echo $city->id; ?>">
                        <td><b><?php echo $city->heading; ?></b></td>
                        <td><img class="video_thumb" src="<?php echo base_url().'assets/admin/images/video_thumb/'.$city->video_thumb; ?>"></td>
                        <td>
                        <video width="400" controls>
                            <source src="<?php echo base_url().'assets/admin/videos/'.$city->video_file; ?>" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <td>
                            <!-- <a id="update_<?php echo $city->id; ?>" class="btn btn-success btn-update">Update</a>-->
                            <a id="del_<?php echo $city->id; ?>" data-file="<?php echo $city->video_file; ?>" class="btn btn-danger btn-del">Delete</a> 
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div id="videoModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add a video</h5>
        <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form enctype="multipart/form-data" id="addVideo">
            <div class="container-fluid">
                <div class="form-group">
                    <label for="exampleInputEmail1">Title:</label>
                    <input id="videoTitle" name="heading" type="text" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail2">Upload video:</label>
                    <input id="videoFile" accept="video/*" name="video_file" type="file" />
                    <span id="err-msg"></span>
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail3">Upload video thumbnail:</label>
                    <input id="imageFile" accept="image/*" name="video_thumb" type="file" />
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" onclick="$('#err-msg').html('');" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>

$(function(){

    var dTable = $('#dataTable').DataTable();

    $("#addVideo").submit(function(event) {
            event.preventDefault();
        }).validate({
            rules: {
                heading:{
                    required:true
                },
                video_file:{
                    required:true
                },
                video_thumb:{
                    required:true
                }
            },
        submitHandler: function(form) {
            $('button[type="submit"]').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url(); ?>add/trainingvideo',
                type: 'POST',
                data: new FormData(form),
                dataType:'json',
                processData: false,
                contentType: false,
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                        $('button[type="submit"]').prop('disabled', false);
                        location.reload();
                    }
                    else if(as.status == false){
                        alert(as.message);
                        $('button[type="submit"]').prop('disabled', false);
                    }
                }
            });
        }
    });

    $('.btn-del').click(function(){
        var id = $(this).attr('id');
        var video_file = $(this).attr('data-file');
        if (confirm("Do you really want to delete this video?") == true) {
            var obj = {id:id.split("_")[1], video_file: video_file};
            $.ajax({
                url:'<?php echo base_url(); ?>delete/trainingvideos',
                type: 'POST',
                data: obj,
                dataType:'json',
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                        location.reload();
                    }
                    else{
                        alert("Error while updating");
                    }
                }
            });
        } else {
            
        }
    });

});
</script>