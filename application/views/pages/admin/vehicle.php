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
        <a href="#">Vehicles</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allVehicles = $this->admin->getAllVehicles(); ?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#cityModal">Add a vehicle</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>S. No.</th>
                    <th>Vehicle Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; foreach ($allVehicles['result'] as $vehicle) { $i++; ?>
                <tr id="b_<?php echo $vehicle->id; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo $vehicle->vehicle_name; ?></td>
                    <td>
                        <a id="del_<?php echo $vehicle->id; ?>" class="btn btn-danger btn-del">Delete</a>
                    </td>
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
        <h5 class="modal-title">Add a Vehicle</h5>
        <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="form-group">
                <label for="exampleInputEmail1">Vehicle type:</label>
                <input id="cityName" type="text" placeholder="Enter a vehicle type" class="form-control" />
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

$(function(){
    var dTable = $('#dataTable').DataTable();

    $('#addCity').click(function(){
        if($('#cityName').val() == ""){
            alert("Vehicle type should not be empty");
        }
        else{
            var cityObj = {vehicle_name:$('#cityName').val()};

            $.ajax({
                    url:'<?php echo base_url(); ?>add/vehicle',
                    type: 'POST',
                    data: cityObj,
                    dataType:'json',
                    success:function(as){
                        if(as.status){
                            $('#cityName').val("");
                            $('#err-msg').html(as.message);
                            
                            var newRow = dTable.row.add( [
                                cityObj.vehicle_name,
                                '<a id="del_'+as.id+'" class="btn btn-danger btn-del">Delete</a>'
        
                            ] ).draw();
                            newRow.nodes().to$().attr('id', 'b_'+as.id);
                            deletevehicle();
                        }
                    }
            });
        }
    });

    function deletevehicle(){

        $('#dataTable').unbind().on("click", "a.btn-del", function(){
            var id = $(this).attr('id');
            if (confirm("Do you really want to delete this vehicle type?") == true) {
                var obj = {id:id.split("_")[1]};
                $.ajax({
                    url:'<?php echo base_url(); ?>delete/vehicle',
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

    deletevehicle();
});
</script>