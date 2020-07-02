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
    @media only screen and (min-width: 900px) {
        .rate {
            display: none;
        }

        #parent {
            width: 50%;
        }
        .img-aw{
            width: 100px;
        }
        .btn-del{
            color: #fff !important;
        }
        .city-row{
            margin-bottom: 50px;
        }
    }
</style>
<?php $allWorker = $this->admin->getAllWorkers(); $awards = $this->admin->getAllAwards(); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Partner Award</a>
    </li>
    <li class="breadcrumb-item active">All</li>
</ol>
<!--Add testimonial form starts -->
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#aboutModal">Add award</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <table class="table table-bordered" id="dataTable">
        <thead>
            <tr>
                <th>S. No.</th>
                <th>Name</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 0; foreach ($awards as $banner) { $i++; ?>
            <tr id="b_<?php echo $banner->id; ?>">
                <td><?php echo $i; ?></td>
                <td><?php echo isset($this->admin->getAdminProfile($banner->user_id)->name)? $this->admin->getAdminProfile($banner->user_id)->name." (ID - ".$banner->user_id.")" : "NA";  ?></td>
                <td><?php if($banner->file == ""){echo "NA";}else{ if(!file_exists('assets/admin/images/documents/'.$banner->file)){echo "NA";}else{  ?><img class="img-responsive img-aw" alt="Not available" src="<?php echo base_url().'assets/admin/images/documents/'.$banner->file;  ?>" /><?php }} ?></td>
        
                <td><a id="del_<?php echo $banner->id; ?>" data-file="<?php echo $banner->file; ?>" class="btn btn-danger btn-del">Delete</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>

    <div id="aboutModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add award</h5>
                    <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="addBanners">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <label>Select Partner:</label>
                                <select class="form-control" name="user_id" id="workers">
                                    <?php foreach ($allWorker['result'] as $worker) { ?>
                                        <option value="<?php echo $worker->id; ?>"><?php echo $worker->name; ?>(ID - <?php echo $worker->id; ?>)</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Upload image:</label>
                                <input type="file" name="file">
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
</div>
<script>
    window.onload = function() {
        $('#dataTable').DataTable();
        $('#workers').select2();

        $("#addBanners").submit(function(event) {
            event.preventDefault();
        }).validate({
            rules: {

            },
            submitHandler: function(form) {

                $.ajax({
                    url: '<?php echo base_url(); ?>add/award',
                    type: 'POST',
                    data: new FormData(form),
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

        $('#dataTable').on("click", ".btn-del", function(){
            var id = $(this).attr('id');
            var file =  $(this).attr('data-file');
            if (confirm("Do you really want to delete this award?") == true) {
                var obj = {id:id.split("_")[1], file: file};
                $.ajax({
                    url:'<?php echo base_url(); ?>delete/award',
                    type: 'POST',
                    data: obj,
                    dataType:'json',
                    success:function(as){
                        if(as.status == true){
                            alert(as.message);
                            $('#b_'+id.split("_")[1]).remove();
                        }
                        else{
                            alert("Error while updating");
                        }
                    }
                });
            } else {
                
            }
        });
    }
</script>