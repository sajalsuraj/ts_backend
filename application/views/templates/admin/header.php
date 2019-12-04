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
  <link href="<?php echo base_url(); ?>assets/admin/css/style.css" rel="stylesheet">

  <!-- Editor style -->
  <link href="<?php echo base_url(); ?>assets/admin/css/richtext.min.css" rel="stylesheet">

  <!-- Bootstrap core JavaScript-->
  <script src="<?php echo base_url(); ?>assets/admin/vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo base_url(); ?>assets/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Page level plugin JavaScript-->
  <script src="<?php echo base_url(); ?>assets/admin/vendor/chart.js/Chart.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/vendor/datatables/jquery.dataTables.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/vendor/datatables/dataTables.bootstrap4.js"></script>

  <!-- Validation library-->
  <script src="<?php echo base_url(); ?>assets/admin/js/jquery.validate.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/admin/js/additional-methods.min.js"></script>

  <!-- Editor style -->
  <script src="<?php echo base_url(); ?>assets/admin/js/jquery.richtext.min.js"></script>

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

    <a class="navbar-brand mr-1" href="index.html">TroubleShooter</a>

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
          <!-- <a class="dropdown-item" href="#">Settings</a>
          <a class="dropdown-item" href="#">Activity Log</a>
          <div class="dropdown-divider"></div> -->
          <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
        </div>
      </li>
    </ul>

  </nav>

  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="profile">
        <i class="far fa-id-badge"></i>
          <span>Profile</span>
        </a>
      </li>

      <?php if($usertype){ ?>
      <li class="nav-item active">
        <a class="nav-link" href="kyc-verify">
        <i class="far fa-address-card"></i>
          <span>Verify KYCs</span>
        </a>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-images"></i>
          <span>Banner</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="add-banner">Add a banner</a>
          <a class="dropdown-item" href="all-banners">View All</a>
        </div>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-hands-helping"></i>
          <span>Services</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="add-service">Add a service</a>
          <a class="dropdown-item" href="all-services">View All</a>
        </div>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="cities">
        <i class="fa fa-location-arrow" aria-hidden="true"></i>
          <span>Cities</span>
        </a>
      </li>

      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-file"></i>
          <span>Static Pages</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <a class="dropdown-item" href="terms">Terms & Conditions</a>
          <a class="dropdown-item" href="privacy-policy">Privacy Policy</a>
        </div>
      </li>

      <?php }else{ ?>
      <li class="nav-item active">
        <a class="nav-link" href="about-me">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>About Me</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="bank-details">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Bank Details</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="requests">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Requests</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="orders">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Orders</span>
        </a>
      </li>
      <?php } ?>
     
    </ul>

    <div id="content-wrapper">

      <div class="container-fluid">
      <?php } else {echo "<div>";} ?>