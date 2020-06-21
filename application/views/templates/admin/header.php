<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Troubleshooter Admin Panel</title>

  <!-- Custom fonts for this template-->
  <link href="<?php echo base_url(); ?>assets/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Page level plugin CSS-->
  <link href="<?php echo base_url(); ?>assets/admin/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?php echo base_url(); ?>assets/admin/css/sb-admin.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/admin/css/style.css?v=3.1" rel="stylesheet">

  <!-- jQuery UI -->
  <link href="<?php echo base_url(); ?>assets/admin/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Editor style -->
  <link href="<?php echo base_url(); ?>assets/admin/css/richtext.min.css" rel="stylesheet">

  <!-- Select2 -->
  <link href="<?php echo base_url(); ?>assets/admin/css/select2.min.css" rel="stylesheet">

  <!-- Bootstrap core JavaScript-->
  <script src="<?php echo base_url(); ?>assets/admin/vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Select2 -->
  <script src="<?php echo base_url(); ?>assets/admin/js/select2.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo base_url(); ?>assets/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Page level plugin JavaScript-->
  <script src="<?php echo base_url(); ?>assets/admin/vendor/chart.js/Chart.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/vendor/datatables/jquery.dataTables.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/vendor/datatables/dataTables.bootstrap4.js"></script>

  <!-- Validation library-->
  <script src="<?php echo base_url(); ?>assets/admin/js/jquery.validate.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/js/additional-methods.min.js"></script>

  <!-- Underscore -->
  <script src="<?php echo base_url(); ?>assets/admin/js/underscore-min.js"></script>

  <script src="<?php echo base_url(); ?>assets/admin/js/jquery-ui.js"></script>

  <!-- Editor style -->
  <script src="<?php echo base_url(); ?>assets/admin/js/jquery.richtext.min.js"></script>
  <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>

</head>
<?php 
    $link = $_SERVER['REQUEST_URI'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>

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
        
    }
} else {
 
}
?>

<body id="page-top">
  <?php if($page !== "login"){ ?>
  <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <a class="navbar-brand mr-1" href="<?php echo base_url(); ?>users/dashboard">TroubleShooters</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search -->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
     
    </form>

    <!-- Navbar -->
    <ul class="navbar-nav ml-auto ml-md-0">
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-user-circle fa-fw"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
          <!-- <a class="dropdown-item" href="#">Settings</a>-->
          <a class="dropdown-item" href="profile">Profile</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
        </div>
      </li>
    </ul>

  </nav>

  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/profile">
        <i class="far fa-id-badge"></i>
          <span>Profile</span>
        </a>
      </li>

      <?php if($usertype){ ?>
      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/kyc-verify">
        <i class="far fa-address-card"></i>
          <span>Workers KYC Verify</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/home-content">
        <i class="fas fa-home"></i>
          <span>App Homepage</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/all-worker">
        <i class="fas fa-user-tie"></i>
          <span>Workers</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/all-customers">
        <i class="fas fa-users"></i>
          <span>Customers</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/all-requests">
        <i class="fas fa-hand-paper"></i>
          <span>Service Requests</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/all-bookings">
        <i class="fas fa-list"></i>
          <span>Bookings</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/all-referrals">
        <i class="fa fa-user-plus" aria-hidden="true"></i>
          <span>Referrals</span>
        </a>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-images"></i>
          <span>Banner</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/add-banner">Add a banner</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/all-banners">View All</a>
        </div>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-box-open"></i>
          <span>Packages</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/add-package">Create Package</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/all-packages">View All</a>
        </div>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user-check"></i>
          <span>Memberships</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/add-membership">Create Membership</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/all-memberships">View All</a>
        </div>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-hands-helping"></i>
          <span>Services</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/add-service">Add a service</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/all-services">View All</a>
        </div>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-question-circle"></i>
          <span>FAQ</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/add-faq-title">Add faq title</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/all-faq-title">View All FAQ Titles</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/add-faq-content">Add faq</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/all-faq">View All FAQs</a>
        </div>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/cities">
        <i class="fa fa-location-arrow" aria-hidden="true"></i>
          <span>Cities</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/notification">
        <i class="fas fa-bell" aria-hidden="true"></i>
          <span>Notifications</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/vehicle">
        <i class="fas fa-motorcycle"></i>
          <span>Vehicles</span>
        </a>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-file"></i>
          <span>Static Pages</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/terms">Terms & Conditions</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/privacy-policy">Privacy Policy</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/how-it-works">How it works</a>
          <a class="dropdown-item" href="<?php echo base_url(); ?>users/declaration">Declaration</a>
        </div>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/training-centres">
        <i class="fas fa-chalkboard-teacher"></i>
          <span>Training Centres</span>
        </a>
      </li>

      <!-- <li class="nav-item active">
        <a class="nav-link" href="notify-vendors">
        <i class="fas fa-bell"></i>
          <span>Notify Vendors</span>
        </a>
      </li> -->

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/contact-us">
        <i class="fas fa-phone-square-alt"></i>
          <span>Contact Us</span>
        </a>
      </li>

      <?php }else{ ?>
      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/about-me">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>About Me</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/bank-details">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Bank Details</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/requests">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Requests</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(); ?>users/orders">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Orders</span>
        </a>
      </li>
      <?php } ?>
     
    </ul>

    <div id="content-wrapper">

      <div class="container-fluid">
      <?php } else {echo "<div>";} ?>