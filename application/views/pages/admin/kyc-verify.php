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
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Workers</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allWorker = $this->admin->getAllKYC();

?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Name Provided</th>
                    <th>ID</th>
                    <th>ID Number</th>
                    <th>ID Front Image</th>
                    <th>ID Back Image</th>
                    <th>Parent Name</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Permanent Address</th>
                    <th>Current Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allWorker as $worker) { ?>
                <tr>
                    <td><?php echo $worker->username; ?></td>
                    <td><?php echo $worker->name; ?></td>
                    <td><?php echo $worker->id_type; ?></td>
                    <td><?php echo $worker->id_number; ?></td>
                    <td><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/documents/<?php echo $worker->img_front_side; ?>" /></td>
                    <td><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/documents/<?php echo $worker->img_back_side; ?>" /></td>
                    <td><?php echo $worker->parent_name; ?></td>
                    <td><?php echo $worker->gender; ?></td>
                    <td><?php echo $worker->dob; ?></td>
                    <td><?php echo $worker->p_house_no.", ".$worker->p_street.", ".$worker->p_city.", ".$worker->p_pincode; ?></td>
                    <td><?php echo $worker->c_house_no.", ".$worker->c_street.", ".$worker->c_city.", ".$worker->c_pincode; ?></td>
                    <?php if($worker->is_verified != 1){ ?>
                        <td><button id="verify_<?php echo $worker->id; ?>_<?php echo $worker->id_number; ?>" type="button" class="btn btn-success btn-verify"><?php if($worker->is_verified != 1){echo "Verify";}else{echo "Verified";} ?></button></td>
                    <?php }else{ ?>
                        <td>Verified</td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#dataTable').DataTable();
    //Function to update the status
    $('.btn-verify').click(function(){
        var detail = $(this).attr('id'), status = 1, id = "";
        var obj = {id:detail.split('_')[1], status:status, type:"kyc"};
        $.ajax({
                url:'<?php echo base_url(); ?>update/kyc',
                type: 'POST',
                data: obj,
                dataType:'json',
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
    });
</script>