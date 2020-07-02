<?php
$roles = [];
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "superadmin" || $this->session->userdata('type') == "admin" || $this->session->userdata('type') == "worker") {
        if($this->session->userdata('type') == "admin" || $this->session->userdata('type') == "superadmin"){
            $usertype = true;
            if($this->session->userdata('type') == "admin"){
                $roles = explode(",", $this->session->userdata('roles'));
            }
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
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
    <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Overview</li>
</ol>
<?php
$allServices = $this->admin->getAllServices();
$totalServices = count($allServices['result']);
$allWorkers = $this->admin->getAllWorkers();
$totalWorkers = count($allWorkers['result']);
$allCustomers = $this->admin->getAllCustomers();
$totalCustomers = count($allCustomers['result']);
$allRequests = $this->admin->getAllRequests();
$totalRequests = count($allRequests);
$allBookings = $this->admin->getAllBookingsDashboard();
$totalBookings = count($allBookings);
$allReferrals = $this->user->getCustomersWhoAreReferred();
$allMemberships = $this->admin->getAllMemberships();
?>

<!-- Icon Cards-->
<div class="row">

 
    
    <?php if($usertype){ ?>
        <?php if($this->session->userdata('type') == "admin"?(in_array("workers", $roles)?true:false):true){ ?>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="mr-5"><?php echo $totalWorkers; ?> Partners!</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="all-worker">
                <span class="float-left">View All</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($this->session->userdata('type') == "admin"?(in_array("services", $roles)?true:false):true){ ?>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-success o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                </div>
                <div class="mr-5"><?php echo $totalServices; ?> Services</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="all-services">
                <span class="float-left">View All</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($this->session->userdata('type') == "admin"?(in_array("customers", $roles)?true:false):true){ ?>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-primary o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="mr-5"><?php echo $totalCustomers; ?> Customers!</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="all-customers">
                <span class="float-left">View All</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($this->session->userdata('type') == "admin"?(in_array("service_requests", $roles)?true:false):true){ ?>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                <i class="fas fa-hands-helping"></i>
                </div>
                <div class="mr-5"><?php echo $totalRequests; ?> Service Requests</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="all-requests">
                <span class="float-left">View All</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($this->session->userdata('type') == "admin"?(in_array("bookings", $roles)?true:false):true){ ?>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-primary o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                <i class="fas fa-list"></i>
                </div>
                <div class="mr-5"><?php echo $totalBookings; ?> Bookings</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="all-bookings">
                <span class="float-left">View All</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($this->session->userdata('type') == "admin"?(in_array("referrals", $roles)?true:false):true){ ?>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                <i class="fa fa-user-plus"></i>
                </div>
                <div class="mr-5"><?php echo count($allReferrals); ?> Referrals</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="all-referrals">
                <span class="float-left">View All</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($this->session->userdata('type') == "admin"?(in_array("memberships", $roles)?true:false):true){ ?>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                <i class="fas fa-user-check"></i>
                </div>
                <div class="mr-5"><?php echo count($allMemberships); ?> Memberships</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="all-memberships">
                <span class="float-left">View All</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
        <?php } ?>

    <?php }else{ ?>
        <?php 
        if(!$this->admin->checkKYCById($this->session->userdata('user_id'), 'kyc')){
            $isKyc = 0;
        }
        else{
            $steps_filled = $this->admin->checkKYCStepsById($this->session->userdata('user_id'), 'kyc');            
            $isKyc = (int) $steps_filled->steps_filled;
        }
        $is_verified = $this->admin->checkIfKYCVerified($this->session->userdata('user_id'), "kyc"); ?>
        
        <?php if($isKyc){
            if($is_verified->is_verified == 1){
                $msg = "Your profile is verified and live now.";
                $bg = 1;
            }
            else{
                $msg = "Your profile is not live, keep checking your profile for the update";
                $bg = 0;
            }
            
         } else{
            $msg = "Profile not live. You need to fill the KYC form to get your profile verified";
            $bg = 0;
         } ?>

        <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white <?php if($bg == 1){ ?> bg-success <?php }else{ ?> bg-danger <?php } ?> o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-fw fa-life-ring"></i>
                </div>
                <div class="mr-5"><?php echo $msg; ?></div>
                </div>
                <!-- <a class="card-footer text-white clearfix small z-1" href="<?php if($isKyc > 0){ echo "view-kyc"; }else{ echo "kycform-1"; } ?>">
                <span class="float-left"><?php if($isKyc > 0){ echo "View KYC"; }else{ echo "Fill the form"; } ?></span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span> -->
                </a>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
                <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-fw fa-life-ring"></i>
                </div>
                <div class="mr-5"><?php if($isKyc >= 4){ echo "Review your KYC"; }else{ echo "Complete your KYC"; } ?></div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="<?php if($isKyc >= 4){ echo "view-kyc"; }else{ echo "kycform-1"; } ?>">
                <span class="float-left"><?php if($isKyc >= 4){ echo "View KYC"; }else{ echo "Fill the form"; } ?></span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
                </a>
            </div>
        </div>
    <?php } ?>
    
</div>