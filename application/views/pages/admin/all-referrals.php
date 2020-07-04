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
.grey-font{
    color: grey;
}
.red-font{
    color: red;
}
.green-font{
    color:green;
}
.city-row{
    margin-bottom: 50px;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Referrals</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allReferrals = $this->user->getCustomersWhoAreReferred();
$amount = $this->admin->getReferralAmount();
?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#referralModal">Add/Edit referral amount</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>S. No.</th>
                    <th>Referrer User ID</th>
                    <th>Referrer Name</th>
                    <th>Referee User ID</th>
                    <th>Referee Name</th>
                    <th>Referral Code</th> 
                </tr>
            </thead>
            <tbody>
            <?php $i =0; foreach ($allReferrals as $ref) { $i++; ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><b><?php echo $ref->referred_by_id; ?></b></td>
                    <td><?php echo $ref->referred_by_name; ?></td>
                    <td><?php echo $ref->referred_id; ?></td>
                    <td><?php echo $ref->referred_name; ?></td>
                    <td><b><?php echo $ref->referral_code; ?></b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div id="referralModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add/Edit Referral</h5>
        <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="form-group">
                <label for="exampleInputEmail1">Referral amount:</label>
                <input id="referralAmount" placeholder="Enter amount..." type="number" value="<?php if($amount != NULL){echo $amount->amount;} ?>" class="form-control" />
                <span id="err-msg"></span>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="addReferral" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-secondary" onclick="$('#err-msg').html('');" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
    $('#dataTable').DataTable({"scrollX": true});
    var isFirstTime = 0;
    var amountId = "";
    $('#addReferral').click(function(){
        if($('#referralAmount').val() == ""){
            alert("Amount should not be empty");
        }
        else{
            var cityObj = {amount:$('#referralAmount').val()};
            <?php $url = ""; if($amount != NULL){ $url = "update/referral"; }else{ $url = "add/referral"; } ?>
            var url = "<?php echo base_url().$url; ?>";
            <?php if($amount != NULL){ ?>
                isFirstTime = 1;
                amountId = <?php echo $amount->id; ?>;
            <?php } ?>
            isFirstTime++;
            if(isFirstTime > 1){
                url = "<?php echo base_url(); ?>update/referral";
                cityObj = {amount:$('#referralAmount').val(), id:amountId};
            }

            $.ajax({
                    url: url,
                    type: 'POST',
                    data: cityObj,
                    dataType:'json',
                    success:function(as){
                        if(as.status){
                            if(as.id){
                                amountId = as.id;
                            }
                            $('#err-msg').html(as.message);
                        }
                    }
            });
        }
    });
</script>