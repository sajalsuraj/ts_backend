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
.p-imags{
    width: 180px;
}
</style>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Membership</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>

<?php $allPackages = $this->admin->getAllMemberships(); ?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Services</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allPackages as $p) { ?>
                <tr>
                    <td><b><?php echo $p->name; ?></b></td>
                    <td>
                    <?php 
                        if($p->services != ""){
                            $services = json_decode($p->services, true);
                            $mode = "";
                            foreach ($services as $val) {
                                if($val['mode']=="rate_per_min"){
                                    $mode = "(per min)";
                                }
                                else{
                                    $mode = "(fixed)";
                                }
                                echo nl2br("*<b>".$this->admin->getServiceById($val['service'])->service_name."</b> : ₹ ".$val['price']." ".$mode.", <b>Quantity: </b>".$val['quantity']."\n");
                            }
                        }
                    ?>
                    </td>
                    <td><?php if($p->image != ""){ ?><img class="p-imags" src="<?php echo base_url().'assets/images/'.$p->image; ?>" /><?php } ?></td>
                    <td><?php if($p->status == 1){ ?><b><span class="green-font">Activated</span></b><?php }else{ ?><span class="red-font">Deactivated</span><?php } ?></td>
                    <td><?php echo $p->from_date; ?></td>
                    <td><?php echo $p->to_date; ?></td>
                    <td>₹ <?php echo $p->price; ?></td>
                    <td>
                        <?php
                            if($p->created_at != ""){
                                $timeCreated = new DateTime('@'.$p->created_at);
                                echo $timeCreated->format('Y-m-d H:i');
                            }
                        ?>
                    </td>
                    <td><button class="btn btn-success">Edit</button> <button class="btn btn-danger">Delete</button></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$('#dataTable').DataTable();
</script>