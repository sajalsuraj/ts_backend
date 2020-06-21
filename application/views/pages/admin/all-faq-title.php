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
a.btn{
    color: #fff !important;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">FAQ Titles</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allFaq = $this->admin->getAllFAQTitles(); ?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>S. No.</th>
                    <th>Title</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; foreach ($allFaq['result'] as $faq) { $i++; ?>
                <tr id="faq_<?php echo $faq->id; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo $faq->title; ?></td>
                    <td><?php echo $faq->created_at; ?></td>
                    <td>
                        <a href="edit-faq-title/<?php echo $faq->id; ?>" class="btn btn-primary">Edit</a>
                        <button id="faq_<?php echo $faq->id; ?>" type="button" class="btn btn-danger btn-delete">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$('#dataTable').DataTable();
$('#dataTable').on("click", ".btn-delete", function(){
        var id = $(this).attr('id');
        if (confirm("Do you really want to delete this FAQ title? All related FAQ content will also get deleted") == true) {
            var obj = {id:id.split("_")[1]};
            $.ajax({
                url:'<?php echo base_url(); ?>delete/faqtitle',
                type: 'POST',
                data: obj,
                dataType:'json',
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                        $('#faq_'+id.split("_")[1]).remove();
                    }
                    else{
                        alert("Error while deleting");
                    }
                }
            });
        } else {
            
        }
    });
</script>