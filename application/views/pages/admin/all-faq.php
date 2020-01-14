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
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">FAQ</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allFaq = $this->admin->getAllFAQs(); ?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allFaq as $faq) { ?>
                <tr>
                    <td><?php echo $faq->title; ?></td>
                    <td><?php echo $faq->faq_category; ?></td>
                    <td><?php echo $faq->faq_description; ?></td>
                    <td><?php echo $faq->created_at; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$('#dataTable').DataTable();
</script>