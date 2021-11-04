<?php 
    session_start();
    //redirect to log in page if not logged in
    if (!isset($_SESSION['loggedIN'])){
        
        header('Location: login.php');
        exit();
    }
    
    //logs out if tried to visit from wrong userType
    if($_SESSION['userType'] != "doctor"){
        header('Location: logout.php');
        exit();
    }
    //logs out if tried to visit before searching for patient
    if (!isset($_SESSION['patient_id'])){
        
        header('Location: logout.php');
        exit();
    }
    //Go back to DoctorHome page
    if (isset($_POST['back'])){
            unset($_SESSION['edit_id']);
            
            exit('back');
    }

    if (isset($_POST['edit'])){
        $orderID = $_SESSION['edit_id']; 
        $drugPres = $_POST['drugPrescriptionPHP'];
        $drugDose = $_POST['drugDosePHP'];
        $drugFrequency = $_POST['drugFrequencyPHP'];
        $drugUOM = $_POST['drugUOMPHP'];
        $drugRoute = $_POST['drugRoutePHP'];
        $drugDuration = $_POST['drugDurationPHP'];

        $connection = new mysqli('localhost', 'root', '','testestdb');
        if($connection->query("UPDATE prescriptionhasorder SET drug_prescription = '$drugPres', dose = $drugDose,frequency='$drugFrequency',
        UOM='$drugUOM',Route='$drugRoute',stop_after='$drugDuration' WHERE prescription_order_id = $orderID;") === TRUE){
            exit('editSuccess');
        }else{
            exit('failed');
        }
        
    }
    
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor</title>
    <link rel="stylesheet" href=".\css\main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            console.log('page ready: DoctorHasPatient');

            $("#backButton").on('click', function (){

               $.ajax(
                    {
                        url: 'doctorEditPrescription.php',
                        method: 'POST',
                        data:{
                            back: 1
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('back') >= 0){

                                window.location = 'doctorViewPrescription.php';
                                
                            }else{
                                alert("Please try again");
                            }
                            
                        },
                        dataType: 'text'
                    }
               );
            });

            $("#submitButton").on('click', function (event){  

                var drugDose = $("#drug--dose").val();
                var drugPrescription = $("#drug--prescription").val();
                var drugFrequency = $("#drug--frequency").val();
                var drugUOM = $("#drug--UOM").val();
                var drugRoute = $("#drug--route").val();
                var drugDuration = $("#drug--duration").val();
                
                $.ajax(
                    {
                    url: 'doctorEditPrescription.php',
                    method: 'POST',
                    data:{
                        edit: 1,
                        drugDosePHP: drugDose,
                        drugPrescriptionPHP: drugPrescription,
                        drugFrequencyPHP: drugFrequency,
                        drugUOMPHP: drugUOM,
                        drugRoutePHP: drugRoute,
                        drugDurationPHP: drugDuration
                    },
                    success: function(response){
                        $("#response").html(response);

                        if(response.indexOf('editSuccess') >= 0){
                            window.location = 'doctorViewPrescription.php';
                        }else{
                            alert("Please try again");
                        }
             

             
                    },
                    dataType: 'text'
                    }
                );
            });
                
        });

        </script>
    
    
</head>
<body>
    <nav>
        <img src="img\doctor.png" alt="doctor" style="width:7%">
        <div class="nav__name"><h2>Dr. <?php echo("{$_SESSION['name']}"); ?></h2></div>
        <ul>
            <li><a href="logout.php">Log out</a></li>
        </ul>
    </nav>

    <button type="button" class="back__button" id="backButton">Back</button>
    <div class="container">
        
    <?php 
        $editID = $_SESSION['edit_id'];

        $connection = new mysqli('localhost', 'root', '','testestdb');

        $data = $connection->query("SELECT * FROM prescriptionhasorder WHERE prescription_order_id = '$editID'");
        
        if($data->num_rows > 0){
            $row = $data->fetch_assoc();
            $medicineID = $row['medicine_id'];
            $data2 = $connection->query("SELECT * FROM medicine WHERE medicine_id = '$medicineID'");
            if($data2->num_rows > 0){
                $row2 = $data2->fetch_assoc();
                $name = $row2['name'];
                $dose = $row['dose'];
                $drugPres = $row['drug_prescription'];
                $freq = $row['frequency'];
                $UOM = $row['UOM'];
                $route = $row['Route'];
                $duration = $row['stop_after'];

        

    ?>
        <div class="box1">
            <div class="input__Text">Name : </div>
            <div class="input__Text">Dose : </div>
            <div class="input__Text">Drug Prescription : </div>
            <div class="input__Text">Frequency : </div>
            <div class="input__Text">UOM : </div>
            <div class="input__Text">Route : </div>
            <div class="input__Text">Duration : </div>
        </div>
        <div class="box2">
            <form method="POST">    
              <input type="text" name="medicineBox" class="medicine__input" id="drug--name"required="required" disabled <?php echo"value='$name'"?>>
              <input type="number" min ="1" step="1" pattern="\d+" name="medicineBox" class="medicine__input" id="drug--dose"required="required" <?php echo"value='$dose'"?>>
              <input type="text" name="medicineBox" class="medicine__input" id="drug--prescription"required="required"<?php echo"value='$drugPres'"?>>
              <input type="text" name="medicineBox" class="medicine__input" id="drug--frequency"required="required"<?php echo"value='$freq'"?>>
              <input type="text" name="medicineBox" class="medicine__input" id="drug--UOM"required="required"<?php echo"value='$UOM'"?>>
              <input type="text" name="medicineBox" class="medicine__input" id="drug--route"required="required"<?php echo"value='$route'"?>>
              <input type="text" name="medicineBox" class="medicine__input" id="drug--duration"required="required"<?php echo"value='$duration'"?>>
              
            </form>
            <button id="submitButton" class="edit__button" type="button" >Submit</button>
        </div>


        <?php

        }else{
            echo("NOTHING FOUND");
        }
    }else{
        echo("NOTHING FOUND");
    }
        ?>

        
            
            
    </div>
    </div>
   
    
    
    
</body>
</html>