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
.red-font{
    color: red;
}
.green-font{
    color:green;
}
td a{
    color: #fff !important;
}
</style> 
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Workers</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allWorker = $this->admin->getAllWorkers();
?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>S. No.</th>
                    <th>Vendor ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Profession</th>
                    <th>City</th>
                    <th>Verification Status</th>
                    <th>Added on</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $i =0; foreach ($allWorker['result'] as $worker) { $i++; ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $worker->id; ?></td>
                    <td><?php echo $worker->name; ?></td>
                    <td><?php echo $worker->phone; ?></td>
                    <td><?php echo $worker->email; ?></td>
                    <td><?php echo $worker->primary_profession; ?></td>
                    <td><?php echo $worker->city; ?></td>
                    <td><b><?php if($worker->otp_verified == "0"){echo "<span class='red-font'>Not Verified</span>";}else{echo "Verified <i class='fas green-font fa-check-circle'></i>";} ?></b></td>
                    <td><b><?php echo $worker->created_at; ?></b></td>
                    <td><a id="<?php echo $worker->id; ?>" class="btn btn-primary edit-worker">Edit</a> <button id="t_<?php echo $worker->id; ?>" type="button" class="btn btn-danger btn-del">Delete</button></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="verifyPasswordModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify password before proceeding</h5>
                    <button type="button" class="close" onclick="$('#err-msg').html('');$('#newPassword').val('');" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="exampleInputEmail">Enter Password:</label>
                            <input id="newPassword" class="form-control" placeholder="Enter password" type="password" />
                            <span id="err-msg"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="verifyPassword" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" onclick="$('#err-msg').html('');$('#newPassword').val('');" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#dataTable').DataTable();
    $("#dataTable").on("click", ".btn-del", function(){
        var id = $(this).attr('id');
        if (confirm("Do you really want to delete this user ?") == true) {
            var obj = {id:id.split("_")[1], type:"worker" };
            $.ajax({
                url:'<?php echo base_url(); ?>delete/user',
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
    var workerId = "";
    $("#dataTable").on("click", ".edit-worker", function(){
        workerId = $(this).attr('id');
        $('#verifyPasswordModal').modal('show');
    });

    $('#verifyPassword').click(function(){
        if($('#newPassword').val() == ""){
            alert('Password cannot be empty');
        }
        else{
            let formData = new FormData();
            formData.append('id', "<?php echo $this->session->userdata('user_id'); ?>");
            formData.append('password', $('#newPassword').val());
            $.ajax({
                url: '<?php echo base_url(); ?>get/verifyadminpassword',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(as) {
                    if (as.status == true) {
                        location.href="edit-worker/"+workerId;
                    } else if (as.status == false) {
                        alert(as.message);
                    }
                }
            });
        }
    });
</script>