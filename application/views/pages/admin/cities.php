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
.city-row{
    margin-bottom: 50px;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Cities</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allCities = $this->admin->getAllCities();
?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#cityModal">Add a city</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>City Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allCities['result'] as $city) { ?>
                <tr id="b_<?php echo $city->id; ?>">
                    <td><?php echo $city->name; ?></td>
                    <td><a id="del_<?php echo $city->id; ?>" class="btn btn-danger btn-del">Delete</a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div id="cityModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add a City</h5>
        <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="form-group">
                <label for="exampleInputEmail1">City:</label>
                <input id="cityName" type="text" class="form-control" />
                <span id="err-msg"></span>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="addCity" class="btn btn-primary">Add</button>
        <button type="button" class="btn btn-secondary" onclick="$('#err-msg').html('');" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
    var dTable = $('#dataTable').DataTable();
    $('#addCity').click(function(){
        if($('#cityName').val() == ""){
            alert("City name should not be empty");
        }
        else{
            var cityObj = {name:$('#cityName').val()};

            $.ajax({
                    url:'<?php echo base_url(); ?>add/city',
                    type: 'POST',
                    data: cityObj,
                    dataType:'json',
                    success:function(as){
                        if(as.status){
                            $('#cityName').val("");
                            $('#err-msg').html(as.message);
                            
                            var newRow = dTable.row.add( [
                                cityObj.name,
                                '<a id="del_'+as.id+'" class="btn btn-danger btn-del">Delete</a>'
        
                            ] ).draw();
                            newRow.nodes().to$().attr('id', 'b_'+as.id);
                            deletecity();
                        }
                    }
            });
        }
    });

    function deletecity(){
        $('#dataTable').unbind().on("click", "a.btn-del", function(){
            var id = $(this).attr('id');
            if (confirm("Do you really want to delete this city?") == true) {
                var obj = {id:id.split("_")[1]};
                $.ajax({
                    url:'<?php echo base_url(); ?>delete/city',
                    type: 'POST',
                    data: obj,
                    dataType:'json',
                    success:function(as){
                        if(as.status == true){
                            alert(as.message);
                            dTable.row($('#b_'+id.split("_")[1])).remove().draw(false);
                        }
                        else{
                            alert("Error while deleting");
                        }
                    }
                });
            } else {
                
            }
        });
    }

    deletecity();

});
</script>