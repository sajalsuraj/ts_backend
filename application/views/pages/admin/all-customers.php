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
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Customers</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allCustomers = $this->admin->getAllCustomers();
?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Photo</th>
                    <th>Verification Status</th>
                    <th>Registered on</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allCustomers['result'] as $customer) { ?>
                <tr>
                    <td><?php echo $customer->name; ?></td>
                    <td><?php echo $customer->phone; ?></td>
                    <td><?php echo $customer->email; ?></td>
                    <td><?php if($customer->photo == ""){ ?><span class="red-font">Not Uploaded</span><?php }else{ ?><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $worker->img_back_side; ?>" /><?php } ?></td>
                    <td><b><?php if($customer->otp_verified == "0"){echo "<span class='red-font'>Not Verified</span>";}else{echo "Verified <i class='fas green-font fa-check-circle'></i>";} ?></b></td>
                    <td><i><?php echo $customer->created_at; ?></i></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$('#dataTable').DataTable();
</script>