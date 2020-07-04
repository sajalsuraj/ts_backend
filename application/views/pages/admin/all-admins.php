<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin") {
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

    a.edit-worker {
        color: #fff !important;
    }
    .eye-icon{
        margin-left: 5px;
        cursor: pointer;
    }
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Admins</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allAdmins = $this->admin->getAllAdmins();?>

<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Page Access</th>
                    <th>Registered on</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 0;
                foreach ($allAdmins as $admin) {
                    $i++; ?>
                <tr id="admin_<?php echo $admin->id; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo $admin->name; ?></td>
                    <td><?php echo $admin->phone; ?></td>
                    <td><?php echo $admin->email; ?></td>
                    <td><span id="pass_<?php echo $i; ?>">************</span><i data-id="<?php echo $i; ?>" data-password="<?php echo $this->admin->crypt($admin->password, 'd'); ?>" class="fas fa-eye-slash eye-icon"></i></td>
                    <td>
                        <ul>
                        <?php $roles = explode(",",$admin->roles);
                            foreach($roles as $role){
                                switch ($role) {
                                    case "workers":
                                        $role = "Workers";
                                        break;
                                    case "workers_kyc":
                                        $role = "Workers KYC";
                                        break;
                                    case "app_homepage":
                                        $role = "App Homepage";
                                        break;
                                    case "customers":
                                        $role = "Customers";
                                        break;
                                    case "service_requests":
                                        $role = "Service Requests";
                                        break;
                                    case "bookings":
                                        $role = "Bookings";
                                        break;
                                    case "referrals":
                                        $role = "Referrals";
                                        break;
                                    case "partners":
                                        $role = "Partners";
                                        break;
                                    case "contact_us":
                                        $role = "Contact us";
                                        break;
                                    case "packages":
                                        $role = "Packages";
                                        break;
                                    case "memberships":
                                        $role = "Memberships";
                                        break;
                                    case "services":
                                        $role = "Services";
                                        break;
                                    case "faq":
                                        $role = "FAQ";
                                        break;
                                    case "cities":
                                        $role = "Cities";
                                        break;
                                    case "vehicles":
                                        $role = "Vehicles";
                                        break;
                                    case "notification":
                                        $role = "Notifications";
                                        break;
                                    case "static_pages":
                                        $role = "Static Pages";
                                        break;
                                    case "training":
                                        $role = "Training";
                                        break;
                                    default:
                                        $role =  "";
                                }
                                echo "<li>".$role."</li>";
                            }
                        ?>
                        </ul>
                    </td>
                    <td><?php echo $admin->created_at; ?></td>
                    <td>
                    <a id="<?php echo $admin->id; ?>" href="edit-admin/<?php echo $admin->id; ?>" class="btn btn-primary edit-worker">Edit</a>
                            <button id="t_<?php echo $admin->id; ?>" type="button" class="btn btn-danger btn-del">Delete</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $('#dataTable').DataTable({"scrollX": true});
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
    $('#dataTable').on("click", ".btn-del", function() {
        var id = $(this).attr('id');
        if (confirm("Do you really want to delete this admin?") == true) {
            var obj = {
                id: id.split("_")[1]
            };
            $.ajax({
                url: '<?php echo base_url(); ?>delete/admin',
                type: 'POST',
                data: obj,
                dataType: 'json',
                success: function(as) {
                    if (as.status == true) {
                        alert(as.message);
                        $('#admin_' + id.split("_")[1]).remove();
                    } else {
                        alert("Error while deleting");
                    }
                }
            });
        } else {

        }
    });
</script>