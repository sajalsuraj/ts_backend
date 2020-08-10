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
    }

    .green-font {
        color: green;
    }

    .p-imags {
        width: 180px;
    }
</style>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo base_url(); ?>users/all-packages">Package</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>

<?php $allPackages = $this->admin->getAllPackages(); ?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>S. No.</th>
                    <th>Package Name</th>
                    <th>Services</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Show on homepage</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=0; foreach ($allPackages as $p) { $i++; ?>
                    <tr id="package_<?php echo $p->id; ?>">
                        <td><?php echo $i; ?></td>
                        <td><b><?php echo $p->name; ?></b></td>
                        <td>
                            <?php
                            if ($p->services != "") {
                                $services = json_decode($p->services, true);
                                $mode = "";
                                foreach ($services as $val) {
                                    if ($val['mode'] == "rate_per_min") {
                                        $mode = "(per min)";
                                    } else {
                                        $mode = "(fixed)";
                                    }
                                    echo nl2br("*<b>" . $this->admin->getServiceById($val['service'])->service_name . "</b> : ₹ " . $val['price'] . " " . $mode . ", <b>Quantity: </b>" . $val['quantity'] . "\n");
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo strip_tags($p->desc_package); ?></td>
                        <td><?php if ($p->image != "") { ?><img class="img-dash" class="p-imags" src="<?php echo base_url() . 'assets/admin/images/' . $p->image; ?>" /><?php } ?></td>
                        <td><?php if ($p->status == 1) { ?><b><span class="green-font">Activated</span></b><?php } else { ?><span class="red-font">Deactivated</span><?php } ?></td>
                        <td><input class="check-home" id="c_<?php echo $p->id; ?>" type="checkbox" <?php if ($p->show_in_homepage == "on") {
                                                        echo "checked";
                                                    } ?>></td>
                        <td><?php echo $p->from_date; ?></td>
                        <td><?php echo $p->to_date; ?></td>
                        <td>₹ <?php echo $p->price; ?></td>
                        <td>
                            <?php
                            if ($p->created_at != "") {
                                $timeCreated = new DateTime('@' . $p->created_at);
                                echo $timeCreated->format('Y-m-d H:i');
                            }
                            ?>
                        </td>
                        <td><a href="edit-package/<?php echo $p->id; ?>" class="btn btn-success">Edit</a> <button id="p_<?php echo $p->id; ?>" type="button" class="btn btn-danger btn-delete">Delete</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div id="imageModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <img id="img-large" style="width:100%;" src="" alt="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#dataTable').DataTable({"scrollX": true});
    $('#dataTable').on("click", ".btn-delete", function() {
        var id = $(this).attr('id');
        if (confirm("Do you really want to delete this package?") == true) {
            var obj = {
                id: id.split("_")[1]
            };
            $.ajax({
                url: '<?php echo base_url(); ?>delete/package',
                type: 'POST',
                data: obj,
                dataType: 'json',
                success: function(as) {
                    if (as.status == true) {
                        alert(as.message);
                        $('#package_' + id.split("_")[1]).remove();
                    } else {
                        alert("Error while deleting");
                    }
                }
            });
        } else {

        }
    });

    $('.img-dash').click(function(){
        let imgSrc = $(this).attr('src');
        $('#img-large').attr('src',imgSrc);
        $('#imageModal').modal('show');
    });

    $('#dataTable').on("click", ".check-home", function() {
        var id = $(this).attr('id');
        var status = "";
        if(this.checked){
            status = "on";
        }
        if (confirm("Do you really want to update this?") == true) {
            var obj = {
                id: id.split("_")[1],
                type: "packages",
                show_in_homepage: status
            };
            $.ajax({
                url: '<?php echo base_url(); ?>update/checkhomepage',
                type: 'POST',
                data: obj,
                dataType: 'json',
                success: function(as) {
                    if (as.status == true) {
                        alert(as.message);
                    } else {
                        alert(as.message);
                    }
                }
            });
        } else {

        }
    });
</script>