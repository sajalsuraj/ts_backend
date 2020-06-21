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
@media only screen and (min-width: 900px){
    #parent{
        width: 50%;
    }
}
#service{
    padding:0;
}
.service{
    display: flex;
    margin-top: 10px;
}
#service select{
    width: 15%;
}
#service input{
    width: 15%;
    margin-left: 10px;
    margin-right: 10px;
}
#duration{
    display:flex;
    padding:0px;
}
#service button{
    margin-right:10px;
}
#service .mode{
    width: 20%;
    margin-right:10px;
}
#service .quantity{
    margin-right: 10px;
}
</style>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo base_url(); ?>users/all-memberships">Membership</a>
    </li>
    <li class="breadcrumb-item active">Add a new one</li>
</ol>

<?php $services = $this->admin->getAllServices(); ?>
<?php $id = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
$package = $this->admin->membershipById($id); $packageServices = json_decode($package->services, true);?>

<div class="row">
    <div class="col-md-7">

        <form id="addBanners">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo $package->name; ?>" required placeholder="Provide a name..." class="form-control">
            </div>
            <div class="form-group">
                <label>Select a service:</label>
                <div class="col-md-12" id="service">

                </div>
            </div>
            <div class="form-group">
                <label>Image:</label>
                <input type="file" name="image">
            </div>
            <div class="form-group">
                <label>Duration</label>
                <div class="col-md-12" id="duration">
                    <input required type="text" value="<?php echo $package->from_date; ?>" placeholder="Select start date" name="from_date" class="form-control" /> 
                    <span>&nbsp;to&nbsp;</span>
                    <input required type="text" value="<?php echo $package->to_date; ?>" placeholder="Select end date" name="to_date" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label>Price:</label>
                <input required type="number" value="<?php echo $package->price; ?>" name="price" class="form-control" placeholder="Eg: 5000" />
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
   
    $('input[name=from_date]').datepicker(
        {
            minDate:0,
            dateFormat: 'dd-mm-yy',
            changeMonth: true, 
            changeYear: true,
            onSelect: function(dateTxt){
                $('input[name=to_date]').prop('disabled', false);
                $('input[name=to_date]').datepicker('option', 'minDate', dateTxt);
            }
        }
    );

    $('input[name=to_date]').datepicker(
        {
            dateFormat: 'dd-mm-yy',
            changeMonth: true, 
            changeYear: true,
            minDate:  $('input[name=from_date]').val()
        }
    );
    var services = [], count=<?php echo count($packageServices); ?>, countArr = [], pArr = [];
    <?php foreach($services['result'] as $service){ ?>
        services.push({id:<?php echo $service->id; ?>, name: "<?php echo $service->service_name; ?>"});
    <?php } ?>

    pArr = <?php echo $package->services; ?>;

    function allServicesFields(c){
        let opt = "";
        opt = '<div class="service"><select class="form-control" id="ser_'+c+'">';
        for(let i=0; i<services.length;i++){
            opt += '<option value="'+services[i].id+'">'+services[i].name+'</option>';
        }
        opt += '</select>';
        opt += '<input id="price_'+c+'" placeholder="Rate" type="number" class="form-control">';
        opt += '<select id="quantity_'+c+'" class="form-control quantity">';
        for(let i = 1; i < 11; i++){
            opt += '<option value="'+i+'">'+i+'</option>'
        }
        opt += '</select>';
        opt += '<select id="mode_'+c+'" class="form-control mode"><option value="rate_per_min">Rate Per Min</option><option value="fixed">Fixed</option></select>';
        if(c == 1){
            opt += '<button id="add_'+c+'" onclick="addMoreServicesField()" type="button" class="btn-primary">Add +</button></div>';
        }
        else{
            opt += '<button id="add_'+c+'" onclick="addMoreServicesField()" type="button" class="btn-primary">Add +</button> <button id="remove_'+c+'" type="button" class="btn-primary rm-button">Remove -</button></div>';
        }
        $('#service').append(opt);
        
        $('#ser_'+c).val(pArr[c-1]['service']);
        $('#price_'+c).val(pArr[c-1]['price']);
        $('#quantity_'+c).val(pArr[c-1]['quantity']);
        $('#mode_'+c).val(pArr[c-1]['mode']);
        countArr.push(c);

        $('.rm-button').unbind().click(function(e){
            e.preventDefault();
            let rmId = $(this).attr('id');
            rmId = rmId.split('_');
            $('#ser_'+rmId[1]).remove();
            $('#price_'+rmId[1]).remove();
            $('#add_'+rmId[1]).remove();
            $('#quantity_'+rmId[1]).remove();
            $('#remove_'+rmId[1]).remove();
            $('#mode_'+rmId[1]).remove();
            const index = countArr.indexOf(parseInt(rmId[1]));
            if (index > -1) {
                countArr.splice(index, 1);
            }
        });
    }

    function addMoreServicesField(){
        count++;
        let opt = "";
        opt = '<div class="service"><select class="form-control" id="ser_'+count+'">';
        for(let i=0; i<services.length;i++){
            opt += '<option value="'+services[i].id+'">'+services[i].name+'</option>';
        }
        opt += '</select>';
        opt += '<input id="price_'+count+'" placeholder="Rate" type="number" class="form-control">';
        opt += '<select id="quantity_'+count+'" class="form-control quantity">';
        for(let i = 1; i < 11; i++){
            opt += '<option value="'+i+'">'+i+'</option>'
        }
        opt += '</select>';
        opt += '<select id="mode_'+count+'" class="form-control mode"><option value="rate_per_min">Rate Per Min</option><option value="fixed">Fixed</option></select>';
        if(count == 1){
            opt += '<button id="add_'+count+'" onclick="addMoreServicesField()" type="button" class="btn-primary">Add +</button></div>';
        }
        else{
            opt += '<button id="add_'+count+'" onclick="addMoreServicesField()" type="button" class="btn-primary">Add +</button> <button id="remove_'+count+'" type="button" class="btn-primary rm-button">Remove -</button></div>';
        }
        $('#service').append(opt);
        countArr.push(count);
       
        $('.rm-button').unbind().click(function(e){
            e.preventDefault();
            let rmId = $(this).attr('id');
            rmId = rmId.split('_');
            $('#ser_'+rmId[1]).remove();
            $('#price_'+rmId[1]).remove();
            $('#add_'+rmId[1]).remove();
            $('#quantity_'+rmId[1]).remove();
            $('#remove_'+rmId[1]).remove();
            $('#mode_'+rmId[1]).remove();
            const index = countArr.indexOf(parseInt(rmId[1]));
            if (index > -1) {
                countArr.splice(index, 1);
            }
        });
    }


    //First field
    if(count < 1){
        addMoreServicesField();
    }
    else{
        for(let i = 1; i <= count; i++){
            allServicesFields(i);
        }
    }
    
    
    
    $("#addBanners").submit(function(event) { 
            event.preventDefault();
        }).validate({
            rules: {

            },
            submitHandler: function(form) {

                var serArr = [];
                for(var i=0; i < countArr.length; i++){
                    if($('#price_'+countArr[i]).val()==""){
                        alert("Price cannot be empty");
                        return;
                    }
                    serArr.push({"service": $('#ser_'+countArr[i]).val(), "price": $('#price_'+countArr[i]).val(), "quantity": $('#quantity_'+countArr[i]).val(), "mode": $('#mode_'+countArr[i]).val()});
                }

                var fD = new FormData(form);
                fD.append('services', JSON.stringify(serArr));
                fD.append('id', <?php echo $id; ?>);
                $.ajax({
                    url:'<?php echo base_url(); ?>update/membership',
                    type: 'POST',
                    data: fD,
                    dataType:'json',
                    processData: false,
                    contentType: false,
                    success:function(as){
                        if(as.status == true){
                            alert(as.message);
                  
                        }
                        else if(as.status == false){
                            alert(as.message);
                        }
                    }
                });
                
            }
        });
</script>