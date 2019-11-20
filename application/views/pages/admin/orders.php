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
        <a href="#">Booking</a>
    </li>
    <li class="breadcrumb-item active">Orders</li>
</ol>

<?php  $orders = $this->admin->getAllVendorBookings($this->session->userdata('user_id')); ?>

<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <th>Booking ID</th>
                <th>Customer Name</th>
                <th>Customer Phone No.</th>
                <th>Booking Location</th>
                <th>Status</th>
                <th>Created at</th>
            </thead>
            <tbody>
            <?php foreach ($orders as $order) { ?>
                <tr>
                    <td><?php echo $order->booking_id; ?></td>
                    <td><?php echo $order->customer_name; ?></td>
                    <td><?php echo $order->customer_phone; ?></td>
                    <td><?php echo $order->booking_lat.", ".$order->booking_lng; ?></td>
                    <td><?php if($order->booking_status == 1){echo "Confirmed";}else if($order->booking_status == 2){echo "In Progress";}else if($order->booking_status == 3){echo "Paused";}else if($order->booking_status == 4){echo "Cancelled";}else if($order->booking_status == 5){echo "Completed";} ?></td>
                    <td><?php echo $order->created_at; ?></td>
                </tr>  
            <?php } ?>      
            </tbody>
        </table>
    </div>
</div>