<?php
if ($this->session->has_userdata('type') == true) {
    if ($this->session->userdata('type') == "worker") {
    } else {
        redirect('users/dashboard');
    }
} else {
    redirect('users/login');
}
?>
<style>
    .city-row{
        margin-bottom: 30px;
    }
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Profile</a>
    </li>
    <li class="breadcrumb-item active">Bank details</li>
</ol>

<?php $user = $this->admin->getBankDetailsById($this->session->userdata('user_id'), 'bank_details'); ?>
<div class="row city-row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#aboutModal">Update bank detail</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Name:</td>
                    <td><?php if ($user) echo $user->name; ?></td>
                </tr>
                <tr>
                    <td>Account No.:</td>
                    <td><?php if ($user) echo $user->ac_no; ?></td>
                </tr>
                <tr>
                    <td>IFSC Code.:</td>
                    <td><?php if ($user) echo $user->ifsc_code; ?></td>
                </tr>
                <tr>
                    <td>Bank Cheque:</td>
                    <td><img class="img-dash" src="<?php if ($user) echo base_url() . 'assets/admin/images/bank_cheque/' . $user->bank_cheque; ?>" style="width: 300px;" /></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="aboutModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add bank details</h5>
                <button type="button" class="close" onclick="$('#err-msg').html('');" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="aboutForm">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Name:</label>
                            <input name="name" type="text" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Bank name:</label>
                            <input name="bank_name" type="text" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">IFSC Code:</label>
                            <input name="ifsc_code" type="text" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Account No.:</label>
                            <input name="ac_no" type="text" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Bank Cheque:</label>
                            <input name="bank_cheque" type="file" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-secondary" onclick="$('#err-msg').html('');" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="imageModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <img id="img-large" style="width:100%;" src="" alt="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
     $('.img-dash').click(function(){
        let imgSrc = $(this).attr('src');
        $('#img-large').attr('src',imgSrc);
        $('#imageModal').modal('show');
    });
    $("#aboutForm").submit(function(event) { 
        event.preventDefault();
    }).validate({
        rules: {
            name:{
                required:true
            },
            bank_name:{
                required:true
            },
            ifsc_code:{
                required:true
            },
            ac_no:{
                required:true
            }
        },
        submitHandler: function(form) {

            var formData = new FormData(form);
            formData.append('user_id', <?php echo $this->session->userdata('user_id'); ?>);
            //formData.append('name', $('#vendorList option:selected').text().split(" - ")[0]);
            
            $.ajax({
                url:'<?php echo base_url(); ?>update/userbankdetails',
                type: 'POST',
                data: formData,
                dataType:'json',
                processData: false,
                contentType: false,
                success:function(as){
                    if(as.status == true){
                        alert(as.message);
                        location.reload();
                    }
                    else if(as.status == false){
                        alert(as.message);
                    }
                }
            });
            
        }
    });
</script>