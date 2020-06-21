<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin") {
    } else {
        redirect('users/login');
    }
} else {
    redirect('users/login');
}
?>
<style>
    .table {
        font-size: 12px;
    }

    .red-font {
        color: red;
        font-weight: bold;
    }

    .green-font {
        color: green;
        font-weight: bold;
    }
    a.btn-primary{
        color: #fff !important;
    }
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">KYC</a>
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
                    <th>Vendor ID</th>
                    <th>User Name</th>
                    <th>Name Provided</th>
                    <th>ID</th>
                    <th>ID Number</th>
                    <th>ID Front Image</th>
                    <th>ID Back Image</th>
                    <th>Face Photo</th>
                    <th>Side face photo</th>
                    <th>Full body photo</th>
                    <th>Tool photo</th>
                    <th>Parent Name</th>
                    <th>Vehicle</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Documents Status</th>
                    <th>Permanent Address</th>
                    <th>Current Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allWorker as $worker) { ?>
                    <?php
                    if ($worker->id_type == "PanCard") {
                        $img_front_side = $worker->img_front_side;
                        if ($img_front_side != "") {
                            $worker->is_document_uploaded = true;
                        } else {
                            $worker->is_document_uploaded = false;
                        }
                    } else {
                        $img_front_side = $worker->img_front_side;
                        $img_back_side = $worker->img_back_side;

                        if ($img_front_side != "" && $img_back_side != "") {
                            $worker->is_document_uploaded = true;
                        } else {
                            $worker->is_document_uploaded = false;
                        }
                    }

                    ?>
                    <tr>
                        <td><?php echo $worker->vendor_id; ?></td>
                        <td><?php echo $worker->username; ?></td>
                        <td><?php echo $worker->name; ?></td>
                        <td><?php echo $worker->id_type; ?></td>
                        <td><?php echo $worker->id_number; ?></td>
                        <td><?php if ($worker->img_front_side !=  "") { ?><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/documents/<?php echo $worker->img_front_side; ?>" /><?php } else echo "NA"; ?></td>
                        <td><?php if ($worker->id_type == "PanCard") { ?><span class="green-font">Not required</span><?php } else { ?><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/documents/<?php echo $worker->img_back_side; ?>" /><?php } ?></td>
                        <td><?php if ($worker->face_photo !=  "") { ?><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $worker->face_photo; ?>" /><?php } else echo "NA"; ?></td>
                        <td><?php if ($worker->side_face_photo !=  "") { ?><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $worker->side_face_photo; ?>" /><?php } else echo "NA"; ?></td>
                        <td><?php if ($worker->full_body_photo !=  "") { ?><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $worker->full_body_photo; ?>" /><?php } else echo "NA"; ?></td>
                        <td><?php if ($worker->tool_photo !=  "") { ?><img style="width: 100px;" src="<?php echo base_url(); ?>assets/admin/images/profile/<?php echo $worker->tool_photo; ?>" /><?php } else echo "NA"; ?></td>
                        <td><?php echo $worker->parent_name; ?></td>
                        <td><?php echo $worker->vehicle; ?></td>
                        <td><?php echo $worker->gender; ?></td>
                        <td><?php echo $worker->dob; ?></td>
                        <td><?php if ($worker->is_document_uploaded) { ?><span class="green-font">Uploaded</span> <i class='fas green-font fa-check-circle'></i><?php } else { ?><span class="red-font">Not Uploaded</span><?php } ?></td>
                        <td><?php echo $worker->p_house_no . ", " . $worker->p_street . ", " . $worker->p_city . ", " . $worker->p_pincode; ?></td>
                        <td><?php echo $worker->c_house_no . ", " . $worker->c_street . ", " . $worker->c_city . ", " . $worker->c_pincode; ?></td>
                        <td>
                            <?php if ($worker->is_verified != 1) { ?>
                                <span class="red-font">Not Verified</span>
                            <?php } else { ?>
                        <span class="green-font">Verified</span> <i class='fas green-font fa-check-circle'></i>
                            <?php } ?>
                    </td>
                    <td>
                        <?php if($worker->is_verified != 1){  ?>
                            <button id="verify_<?php echo $worker->id; ?>_<?php echo $worker->id_number; ?>" type="button" class="btn btn-success btn-verify">Verify</button>
                        <?php } else { ?>
                            <button id="verify_<?php echo $worker->id; ?>_<?php echo $worker->id_number; ?>" type="button" class="btn btn-danger btn-unverify">Unverify</button>
                        <?php } ?>
                        &nbsp;<a href="edit-kyc/<?php echo $worker->user_id; ?>" class="btn btn-primary">Edit</a>
                    </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#dataTable').DataTable({
        "scrollX": true
    });
    //Function to update the status
    $("#dataTable").on("click", ".btn-verify", function() {
        var detail = $(this).attr('id'),
            status = 1,
            id = "";
        var obj = {
            id: detail.split('_')[1],
            status: status,
            type: "kyc"
        };
        $.ajax({
            url: '<?php echo base_url(); ?>update/kyc',
            type: 'POST',
            data: obj,
            dataType: 'json',
            success: function(as) {
                if (as.status == true) {
                    alert(as.message);
                    location.reload();
                } else if (as.status == false) {
                    alert(as.message);
                }
            }
        });
    });

    $("#dataTable").on("click", ".btn-unverify", function() {
        var detail = $(this).attr('id'),
            status = 0,
            id = "";
        var obj = {
            id: detail.split('_')[1],
            status: status,
            type: "kyc"
        };
        $.ajax({
            url: '<?php echo base_url(); ?>update/kyc',
            type: 'POST',
            data: obj,
            dataType: 'json',
            success: function(as) {
                if (as.status == true) {
                    alert("KYC unverified");
                    location.reload();
                } else if (as.status == false) {
                    alert(as.message);
                }
            }
        });
    });
</script>