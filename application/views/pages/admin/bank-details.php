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
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Profile</a>
    </li>
    <li class="breadcrumb-item active">Bank details</li>
</ol>

<?php $user = $this->admin->getBankDetailsById($this->session->userdata('user_id'), 'bank_details'); ?>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Name:</td>
                    <td><?php if($user)echo $user->name; ?></td>
                </tr>
                <tr>
                    <td>Account No.:</td>
                    <td><?php if($user)echo $user->ac_no; ?></td>
                </tr>
                <tr>
                    <td>IFSC Code.:</td>
                    <td><?php if($user)echo $user->ifsc_code; ?></td>
                </tr>
                <tr>
                    <td>Bank Cheque:</td>
                    <td><img src="<?php if($user)echo base_url().'assets/admin/images/bank_cheque/'.$user->bank_cheque; ?>" style="width: 300px;" /></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>