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
    <li class="breadcrumb-item active">About</li>
</ol>

<?php $user = $this->admin->getUserAboutById($this->session->userdata('user_id'), 'about'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="col-md-12">
            <a href="edit-about" class="btn btn-success float-right">Edit</a>
        </div>
        <br>
        <br>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Year / Month:</td>
                    <td><?php if($user)echo $user->year." / ".$user->month; ?></td>
                </tr>
                <tr>
                    <td>Business Name:</td>
                    <td><?php if($user)echo $user->business; ?></td>
                </tr>
                <tr>
                    <td>Phone No.:</td>
                    <td><?php if($user)echo $user->phone; ?></td>
                </tr>
                <tr>
                    <td>Website:</td>
                    <td><?php if($user)echo $user->website; ?></td>
                </tr>
                <tr>
                    <td>Introduction:</td>
                    <td><?php if($user)echo $user->intro; ?></td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>

