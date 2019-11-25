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
.btn{
    color: #fff !important;
}
img{
    width: auto;
    height: 200px;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Banners</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allBanners = $this->admin->getAllBanners();
?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allBanners['result'] as $banner) { ?>
                <tr id="b_<?php echo $banner->id; ?>">
                    <td><img class="img-responsive" src="<?php echo base_url(); ?>assets/admin/images/banner/<?php echo $banner->banner_image; ?>" /></td>
                    <td><a id="banner_<?php echo $banner->id; ?>" data-status="<?php echo $banner->status; ?>" class="btn <?php if($banner->status=="true"){echo "btn-success";}else{echo "btn-primary";} ?> btn-status"><?php if($banner->status=="true"){echo "Deactivate";}else{echo "Activate";} ?></a> <a id="del_<?php echo $banner->id; ?>" class="btn btn-danger btn-del">Delete</a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#dataTable').DataTable();

    $('.btn-status').click(function(){
        var id = $(this).attr('id');
        var status = $(this).attr('data-status');
        if(status == "true"){
            if (confirm("Do you really want to deactivate this banner? It will not be shown in the app.") == true) {
                var obj = {id:id.split("_")[1], status:"false"};
                $.ajax({
                    url:'<?php echo base_url(); ?>update/banner',
                    type: 'POST',
                    data: obj,
                    dataType:'json',
                    success:function(as){
                        if(as.status == true){
                            alert(as.message);
                            $('#banner_'+id.split("_")[1]).html('Activate');
                            $('#banner_'+id.split("_")[1]).removeClass('btn-success');
                            $('#banner_'+id.split("_")[1]).addClass('btn-primary');
                            $('#banner_'+id.split("_")[1]).attr('data-status','false');
                        }
                        else{
                            alert("Error while updating");
                        }
                    }
                });
            } else {
                
            }
        }
        else{
            var obj = {id:id.split("_")[1], status:"true"};
            $.ajax({
                url:'<?php echo base_url(); ?>update/banner',
                type: 'POST',
                data: obj,
                dataType:'json',
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                        $('#banner_'+id.split("_")[1]).html('Deactivate');
                        $('#banner_'+id.split("_")[1]).removeClass('btn-primary');
                        $('#banner_'+id.split("_")[1]).addClass('btn-success');
                        $('#banner_'+id.split("_")[1]).attr('data-status','true');
                    }
                    else{
                        alert("Error while updating");
                    }
                }
            });
        }
    });

    $('.btn-del').click(function(){
        var id = $(this).attr('id');
        if (confirm("Do you really want to delete this banner?") == true) {
            var obj = {id:id.split("_")[1]};
            $.ajax({
                url:'<?php echo base_url(); ?>delete/banner',
                type: 'POST',
                data: obj,
                dataType:'json',
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                        $('#b_'+id.split("_")[1]).remove();
                    }
                    else{
                        alert("Error while updating");
                    }
                }
            });
        } else {
            
        }
    });
});
</script>