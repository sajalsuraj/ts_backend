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
@media only screen and (min-width: 900px){
    .rate{
        display:none;
    }
    #parent{
        width: 50%;
    }
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Notify Vendors</a>
    </li>
</ol>
<div class="row">
  <div class="col-md-6">
    <div>
      Search Users : <input id="allUsers" type="text" class="form-control" placeholder="Search and Select User(s)" />
    </div>
    <div id="selectedUsers"></div>
    <div>
      <form id="notificationForm">
        <div class="form-group">
          <label for="">Title</label>
          <input type="text" class="form-control" />
        </div>
        <div class="form-group">
          <label for="">Message</label>
          <textarea class="form-control"></textarea>
        </div>
        <div class="form-group">
          <button class="btn btn-primary" type="submit">Send</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>

</script>