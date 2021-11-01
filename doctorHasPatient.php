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
            unset($_SESSION['patient_id']);
            unset($_SESSION['patient_name']);
            unset($_SESSION['patient_DOB']);
            unset($_SESSION['patient_email']);
            unset($_SESSION['patient_phone']);
            unset($_SESSION['patient_weight']);
            unset($_SESSION['patient_height']);
            unset($_SESSION['patient_gender']);
            unset($_SESSION['patient_address']);
            unset($_SESSION['patient_allergy']);
            exit('back');
    }

    if (isset($_POST['add'])){
        
        exit('adding');
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
                        url: 'doctorHasPatient.php',
                        method: 'POST',
                        data:{
                            back: 1
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('back') >= 0){

                                window.location = 'doctorHome.php';
                                
                            }else{
                                alert("Please try again");
                            }
                            

                            
                        },
                        dataType: 'text'
                    }
               );
            });

            $("#addPrescriptionButton").on('click', function (){

               $.ajax(
                    {
                        url: 'doctorHasPatient.php',
                        method: 'POST',
                        data:{
                            add: 1
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('adding') >= 0){

                                window.location = 'doctorAddPres.php';
                                
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

    
    <div class="container">
        

        <div class="container__patient_details">
            <button type="button" class="back__button" id="backButton">Back</button>
            <h2 > ID = <?php echo("{$_SESSION['patient_id']}"); ?> </h2>
            <h2 > Name = <?php echo("{$_SESSION['patient_name']}"); ?> </h2>
            <h2 > DOB[YYYY/MM/DD] = <?php echo("{$_SESSION['patient_DOB']}"); ?> </h2>
            <h2 > Email = <?php echo("{$_SESSION['patient_email']}"); ?> </h2>
            <h2 > Phone = <?php echo("{$_SESSION['patient_phone']}"); ?> </h2>
            <h2 > Weight(kg) = <?php echo("{$_SESSION['patient_weight']}"); ?> </h2>
            <h2 > Height(cm) = <?php echo("{$_SESSION['patient_height']}"); ?> </h2>
            <h2 > Gender = <?php echo("{$_SESSION['patient_gender']}"); ?> </h2>
            <h2 > Address = <?php echo("{$_SESSION['patient_address']}"); ?> </h2>
            <h2 > Allergy = <?php echo("{$_SESSION['patient_allergy']}"); ?> </h2>
        </div>
        <div class="container__patient_prescription">
            <input type="text" name="searchPrescirptionBox" class="search__prescription" placeholder="Search prescription...">
            <button type="button" class="add__button--prescription" id="addPrescriptionButton">Add Prescription</button>
            <table>
                <th>Date</th>
                <th>Prescription ID</th>
                <th>Requested By</th>
            <?php
                $connection = new mysqli('localhost', 'root', '','testestdb');
                $patient_id = $_SESSION['patient_id'];

                $data = $connection->query("SELECT
                user.name,
                prescription.token_string,
                prescription.pr_date
              FROM user
              JOIN doctor
                ON user.user_id = doctor.user_id
              JOIN prescription
                ON patient_id = $patient_id");
                
                if($data->num_rows > 0){
                    while($row = $data->fetch_assoc()){
                        echo "<tr><td>" . $row['pr_date'] . "</td><td>" . $row['token_string'] . "</td><td>" . $row['name'] . "</td></tr>";
                    }
                    echo"</table>";
                }else{
                    echo"</table><br><h1>No Records</h1>";
                }   
            ?>    
    </div>
    </div>
   
    
    
    
</body>
</html>