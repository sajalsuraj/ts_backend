<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin" || $this->session->userdata('type') == "worker") {
        if($this->session->userdata('type') == "admin"){
            $usertype = true;
        }
        else{
            $usertype = false;
        }
    } else {
        redirect('users/login');
    }
} else {
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
        <a href="#">Worker</a>
    </li>
    <li class="breadcrumb-item active">View your KYC</li>
</ol>
<?php $allWorker = $this->admin->getKycByID($this->session->userdata('user_id'));

?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
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
                        <td>Not verified</td>
                    <?php }else{ ?>
                        <td>Verified</td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>