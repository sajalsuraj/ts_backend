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
.eye-icon1{
    position: absolute;
    right: 0;
    margin-right: 45px;
    cursor: pointer;
    margin-top: -25px;
}
</style> 
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Partners</a>
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
                    <th>Partner ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Password</th>
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
                    <td><span id="pass_<?php echo $i; ?>">************</span><i data-id="<?php echo $i; ?>" data-password="<?php echo $this->admin->crypt($worker->password, 'd'); ?>" class="fas fa-eye-slash eye-icon"></i></td>
                    <td><ul><?php $profs = explode(",",$worker->sub_profession); foreach($profs as $p){ ?>
                        <li><?php echo isset($this->admin->getServiceById($p)->service_name)?$this->admin->getServiceById($p)->service_name:"NA"; } ?></li>
                        </ul>
                    </td>
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
                    <button type="button" class="close" onclick="$('#newPassword').attr('type','password');$('#newPassword').val('');$('.eye-icon1').removeClass('fa-eye');$('.eye-icon1').addClass('fa-eye-slash');" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="exampleInputEmail">Enter Password:</label>
                            <input id="newPassword" class="form-control" placeholder="Enter password" type="password" />
                            <i class="fas fa-eye-slash eye-icon1"></i>
                            <span id="err-msg"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="verifyPassword" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" onclick="$('#newPassword').attr('type','password');$('#newPassword').val('');$('.eye-icon1').removeClass('fa-eye');$('.eye-icon1').addClass('fa-eye-slash');" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#dataTable').DataTable({"scrollX": true});
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

    $("#dataTable").on("click", ".eye-icon", function(){

        if($(this).hasClass('fa-eye-slash')){
            var pass = $(this).attr('data-password');
            var id = $(this).attr('data-id');
            $('#pass_'+id).html(pass);
            $(this).removeClass('fa-eye-slash');
            $(this).addClass('fa-eye');
        }
        else if($(this).hasClass('fa-eye')){
            var id = $(this).attr('data-id');
            $('#pass_'+id).html("************");
            $(this).addClass('fa-eye-slash');
            $(this).removeClass('fa-eye');
        }
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
    $('.eye-icon1').click(function(){
        if($('#newPassword').attr('type') == 'password'){
            $('#newPassword').attr('type','text');
            $(this).removeClass('fa-eye-slash');
            $(this).addClass('fa-eye');
        }
        else{
            $('#newPassword').attr('type','password');
            $(this).removeClass('fa-eye');
            $(this).addClass('fa-eye-slash');
        }
    });
</script>