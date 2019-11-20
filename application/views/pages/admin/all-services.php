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
        <a href="#">Services</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allServices = $this->admin->getAllServices();
?>
<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Rate/Minute</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allServices['result'] as $service) { ?>
                <tr>
                    <td><?php echo $service->service_name; ?></td>
                    <td>&#8377;<?php echo $service->rate_per_min; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>