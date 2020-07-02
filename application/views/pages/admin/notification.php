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
.btn-del{
    color: #fff !important;
}
.grey-font{
    color: grey;
}
.red-font{
    color: red;
}
.green-font{
    color:green;
}
.city-row{
    margin-bottom: 50px;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Notifications</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php 
$allWorkers = $this->admin->getAllWorkers();
$allNotifications = $this->admin->getAllNotifications();
?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#notificationModal">Send notification</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <td>S. No.</td>
                    <th>Notification ID</th>
                    <th>Partner Name</th>
                    <th>Notification Title</th>
                    <th>Message</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 0; foreach ($allNotifications as $not) { $i++; ?>
                <tr id="b_<?php echo $not->id; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo $not->id; ?></td>
                    <?php if($not->vendor_name != NULL){ ?>
                    <td><b><?php echo $not->vendor_name; ?></b> (ID - <?php echo $not->vendor_id; ?>)</td>
                    <?php }else{ ?>
                        <td><b>All Partners</b></td>
                    <?php } ?>
                    <td><?php echo $not->title; ?></td>
                    <td><?php echo $not->message; ?></td>
                    <td><b><?php $timeCreated = new DateTime('@'.$not->created_at); echo $timeCreated->format('Y-m-d H:i'); ?></b></td>
                    <td><a id="del_<?php echo $not->id; ?>" class="btn btn-danger btn-del">Delete</a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div id="notificationModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Notify a vendor</h5>
        <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="notificationForm">
        <div class="modal-body">
            <div class="container-fluid">
                <div class="form-group">
                    <input name="allchecked" type="checkbox" id="selectAll" />
                    <label class="form-check-label" for="selectAll">Select all partners</label>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Select a partner:</label>
                    <select name="vendor_id" class="form-control" id="vendorList">
                        <?php foreach ($allWorkers['result'] as $worker) { ?>
                            <option value="<?php echo $worker->id; ?>"><?php echo $worker->name; ?> - <?php echo $worker->id; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Notification Title:</label>
                    <input name="title" type="text" class="form-control" id="notificationTitle" />
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Notification Message:</label>
                    <textarea class="form-control" name="message" id="notificationMsg"></textarea>
                    <span id="err-msg"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="addReferral" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="$('#err-msg').html('');" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
    $('#dataTable').DataTable();

    $('#selectAll').change(function() {
        if(this.checked) {
            $('#vendorList').prop('disabled', 'disabled');
            $(this).prop("checked", returnVal);
        }
        else{
            $('#vendorList').prop('disabled', false);
        }
    });

    $("#notificationForm").submit(function(event) { 
        event.preventDefault();
    }).validate({
        rules: {

        },
        submitHandler: function(form) {
            
            $.ajax({
                url:'<?php echo base_url(); ?>add/notification',
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

    $('#dataTable').on("click", ".btn-del", function(){
        var id = $(this).attr('id');
        if (confirm("Do you really want to delete this notification?") == true) {
            var obj = {id:id.split("_")[1]};
            $.ajax({
                url:'<?php echo base_url(); ?>delete/notification',
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
</script>