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
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="#">Bookings</a>
    </li>
    <li class="breadcrumb-item active">View All</li>
</ol>
<?php $allBookings = $this->admin->getAllBookingsDashboard();
?>
<div class="row">
    <div class="col-md-12">
        <table id="dataTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Booking No.</th>
                    <th>Request No.</th>
                    <th>Customer Name</th>
                    <th>Vendor Assigned</th>
                    <th>Reached location at</th>
                    <th>Booking OTP</th>
                    <th>OTP status</th>
                    <th>Booking Status</th>
                    <th>Started at</th>
                    <th>Paused at</th>
                    <th>Restarted at</th> 
                    <th>Amount</th> 
                    <th>Payment</th> 
                    <th>Completed at</th> 
                    <th>Created at</th> 
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allBookings as $book) { ?>
                <tr>
                    <td><b><?php echo $book->booking_id; ?></b></td>
                    <td><b><?php echo $book->req_no; ?></b></td>
                    <td><?php echo $book->customer_name; ?></td>
                    <td><?php echo $book->vendor_name; ?></td>
                    <td>
                    <?php 
                     if($book->reached_location_at != ""){
                        $timeReached = new DateTime('@'.$book->reached_location_at);
                        echo $timeReached->format('Y-m-d, H:i');
                     }
                    ?>
                    </td>
                    <td>
                    <?php 
                     echo $book->booking_otp;
                    ?>
                    </td>
                    <td>
                     <span class="<?php if($book->is_otp_verified == "0"){echo "red-font";}else if($book->is_otp_verified == "1"){echo "green-font";}else{echo "red-font";} ?>"><?php if($book->is_otp_verified == "0"){echo "Not Verified";}else{echo "Verified";} ?></span>
                    </td>
                    <td>
                    <?php 
                        $status = "";
                        if($book->booking_status == "1"){
                            $status = "Accepted";
                        }
                        else if($book->booking_status == "2"){
                            $status = "Started";
                        }
                        else if($book->booking_status == "3"){
                            $status = "Paused";
                        }
                        else if($book->booking_status == "4"){
                            $status = "Cancelled";
                        }
                        else if($book->booking_status == "5"){
                            $status = "Completed";
                        }
                    ?>
                    <b><span class="<?php if($book->booking_status == "4"){echo "red-font";}else if($book->booking_status == "5"){echo "green-font";} ?>"><?php echo $status; ?></span></b>
                    </td>
                    <td>
                        <?php 
                            if($book->started_at != ""){
                                $timeStarted = new DateTime('@'.$book->started_at);
                                echo $timeStarted->format('Y-m-d, H:i');
                            }  
                        ?>
                    </td>
                    <td>
                    <?php 
                        if($book->paused_at != ""){
                            $timePaused = new DateTime('@'.$book->paused_at);
                            echo $timePaused->format('Y-m-d, H:i');
                        }  
                    ?>
                    </td>
                    <td>
                    <?php 
                        if($book->restarted_at != ""){
                            $timeRestarted = new DateTime('@'.$book->restarted_at);
                            echo $timeRestarted->format('Y-m-d, H:i');
                        }  
                    ?>
                    </td>
                    <td>
                    <?php 
                        $amount = "";
                        if($book->amount != ""){
                            $amount = $book->amount;
                        }  
                    ?>
                    <b><span><?php echo $amount; ?></span></b>
                    </td>
                    <td>
                    <b><span class="<?php if($book->has_paid == "0"){echo "red-font";}else if($book->has_paid == "1"){echo "green-font";} ?>"><?php if($book->has_paid == "0"){echo "Not Paid";}else if($book->has_paid == "1"){echo "Paid";} ?></span></b>
                    </td>
                    <td>
                    <?php 
                        if($book->completed_at != ""){
                            $timeCompleted = new DateTime('@'.$book->completed_at);
                            echo $timeCompleted->format('Y-m-d, H:i');
                        }  
                    ?>
                    </td>
                    <td>
                    <?php 
                        if($book->created_at != ""){
                            $timeCreated = new DateTime('@'.$book->created_at);
                            echo $timeCreated->format('Y-m-d, H:i');
                        }  
                    ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $('#dataTable').DataTable({"scrollX": true});
</script>