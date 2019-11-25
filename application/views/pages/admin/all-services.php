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
.btn{
    color: #fff !important;
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
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Parent Category</th>
                    <th>Rate/Minute (In INR)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allServices['result'] as $service) { ?>
                <tr id="service_<?php echo $service->id; ?>">
                    <td><?php echo $service->service_name; ?></td>
                    <td><?php if($service->parent_category == ""){echo "None";}else{echo $this->admin->getServiceById($service->parent_category)->service_name;}  ?></td>
                    <td><?php if($service->rate_per_min == ""){echo "NIL";}else{echo "&#8377;".$service->rate_per_min;} ?></td>
                    <td><a href="edit-service/<?php echo $service->id; ?>" class="btn btn-primary">Edit</a> <a id="del_<?php echo $service->id; ?>" class="btn btn-danger btn-del">Delete</a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#dataTable').DataTable();

    $('.btn-del').click(function(){
        var id = $(this).attr('id');
        if (confirm("Do you really want to delete this service? Subcategories related to this service will also be deleted!") == true) {
            var obj = {id:id.split("_")[1]};
            $.ajax({
                url:'<?php echo base_url(); ?>delete/service',
                type: 'POST',
                data: obj,
                dataType:'json',
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                        $('#service_'+id.split("_")[1]).remove();
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