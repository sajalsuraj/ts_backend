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
<?php $allWorker = $this->admin->getAllWorkers();
?>
<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Profession</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allWorker['result'] as $worker) { ?>
                <tr>
                    <td><?php echo $worker->name; ?></td>
                    <td><?php echo $worker->phone; ?></td>
                    <td><?php echo $worker->email; ?></td>
                    <td><?php echo $worker->primary_profession; ?></td>
                    <td><?php echo $worker->city; ?></td>
                    <td><button id="t_<?php echo $worker->id; ?>" type="button" class="btn btn-danger btn-del">Delete</button></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $('.btn-del').click(function(){
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
</script>