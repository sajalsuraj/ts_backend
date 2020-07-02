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
    .table {
        font-size: 12px;
    }

    .red-font {
        color: red;
    }

    .green-font {
        color: green;
    }

    a.edit-worker {
        color: #fff !important;
    }
    .eye-icon{
        margin-left: 5px;
        cursor: pointer;
    }
    .city-row{
        margin-bottom: 50px;
    }
</style>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Partners about section</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allAbout = $this->admin->getAboutSection(); $allWorkers = $this->admin->getAllWorkers();?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#aboutModal">Add new business detail</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Partner Name</th>
                    <th>Partner ID</th>
                    <th>Doing business since</th>
                    <th>Business / Shop name</th>
                    <th>Business / Shop phone</th>
                    <th>Business / Shop website</th>
                    <th>Business / Shop Description</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 0;
                foreach ($allAbout as $about) {
                    $i++; ?>
                <tr id="admin_<?php echo $about->id; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo $about->username; ?></td>
                    <td><?php echo $about->vendor_id; ?></td>
                    <td><?php echo $about->year; ?>/<?php echo $about->month; ?></td>
                    <td><?php echo $about->business; ?></td>
                    <td><?php echo $about->phone; ?></td>
                    <td><?php echo $about->website; ?></td>
                    <td><?php echo $about->intro; ?></td>
                    <td><?php echo $about->created_at; ?></td>
                    <td>
                    <a id="<?php echo $about->id; ?>" href="edit-vendor-about/<?php echo $about->vendor_id; ?>" class="btn btn-primary edit-worker">Edit</a>
                            <button id="t_<?php echo $about->id; ?>" type="button" class="btn btn-danger btn-del">Delete</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="aboutModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add business details</h5>
                    <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="aboutForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Select a partner:</label>
                                <select name="user_id" class="form-control" id="vendorList">
                                    <?php foreach ($allWorkers['result'] as $worker) { ?>
                                        <option value="<?php echo $worker->id; ?>"><?php echo $worker->name; ?> - <?php echo $worker->id; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Business name:</label>
                                <input name="business" type="text" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Business year:</label>
                                <input name="year" type="number" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Business Month:</label>
                                <input name="month" type="number" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Business Phone:</label>
                                <input name="phone" type="number" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Business Website:</label>
                                <input name="website" placeholder="Start with http:// or https://" type="text" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Business Description:</label>
                                <textarea class="form-control" name="intro"></textarea>
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
$('#vendorList').select2();
$('#dataTable').DataTable();
$('#dataTable').on("click", ".btn-del", function(){
    var id = $(this).attr('id');
    if (confirm("Do you really want to delete this data?") == true) {
        var obj = {id:id.split("_")[1]};
        $.ajax({
            url:'<?php echo base_url(); ?>delete/about',
            type: 'POST',
            data: obj,
            dataType:'json',
            success:function(as){
                if(as.status == true){
                    alert(as.message);
                    $('#admin_'+id.split("_")[1]).remove();
                }
                else{
                    alert("Error while deleting");
                }
            }
        });
    } else {
        
    }
});

$("#aboutForm").submit(function(event) { 
    event.preventDefault();
}).validate({
    rules: {
        business:{
            required:true
        },
        intro:{
            required:true
        },
        year:{
            required:true,
            maxlength: 4,
            minlength: 4
        },
        month:{
            required:true,
            maxlength: 2,
            minlength: 1
        },
        phone:{
            required: true,
            maxlength: 10,
            minlength: 10
        }
    },
    submitHandler: function(form) {

        var formData = new FormData(form);
    
        
        $.ajax({
            url:'<?php echo base_url(); ?>update/userabout',
            type: 'POST',
            data: formData,
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