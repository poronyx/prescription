<?php 
    session_start();
    //redirect to log in page if not logged in
    if (!isset($_SESSION['loggedIN'])){
        
        header('Location: login.php');
        exit();
    }
    
    //logs out if tried to visit from wrong userType
    if($_SESSION['userType'] != "pharmacist"){
        header('Location: logout.php');
        exit();
    }

    if (!isset($_SESSION['pres_id'])){
        
        header('Location: login.php');
        exit();
    }

    //Go back to DoctorHome page

    if (isset($_POST['view'])){

        $_SESSION['pres_id'] = $_POST['viewPresIDPHP'];

        exit('viewSuccess');
    }
    if (isset($_POST['search'])){

        $connection = new mysqli('localhost', 'root', '','testestdb');
        $searchString = $_POST['searchPresPHP'];

        $data = $connection->query("SELECT * FROM prescription WHERE token_string = '$searchString' ORDER BY prescription_id DESC");
        if($data->num_rows > 0){
          $row = $data->fetch_assoc();
          $status = $row['collection_status'];
          $patientID = $_SESSION['patient_id'];
          $patientDataID = $row['patient_id'];
          if($patientID."" === $patientDataID.""){
            $_SESSION['pres_id'] = $row['prescription_id'];
            exit('searchComplete');
          }else{
            exit('Failed');
          }
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
    <title>Patient</title>
    <link rel="stylesheet" href=".\css\main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            console.log('page ready: patientHome');

            

            $("#searchPrescriptionButton").on('click', function (){
                var searchPres = $("#searchPrescription").val();
               $.ajax(
                    {
                        url: 'patientHome.php',
                        method: 'POST',
                        data:{
                            search: 1,
                            searchPresPHP: searchPres
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('searchComplete') >= 0){

                                window.location = 'patientViewPrescription.php';
                                
                            }else{
                                alert("Search failed!");
                            }
                            

                            
                        },
                        dataType: 'text'
                    }
               );
            });

            $(".view__button--prescription").on('click', function (event){  
                var buttonClicked = event.target.id ;  
                var viewPresID = $("#"+buttonClicked).val();
                console.log(viewPresID);
            $.ajax(
     {
         url: 'patientHome.php',
         method: 'POST',
         data:{
             view: 1,
             viewPresIDPHP: viewPresID
         },
         success: function(response){
             $("#response").html(response);

             if(response.indexOf('viewSuccess') >= 0){

                window.location = 'patientViewPrescription.php';
                 
             }else{
                 alert("No results");
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
        <img src="img\patient.png" alt="patient" style="width:7%">
        <ul>
            <li><a href="logout.php">Log out</a></li>
        </ul>
    </nav>

    <div class="container">
        

        <div class="container__patient_details">
        <?php 

                $connection = new mysqli('localhost', 'root', '','testestdb');
                $patientID = $_SESSION['user_id'];
                $data = $connection->query("SELECT * FROM user,patient WHERE user.user_id = '$patientID' AND patient.user_id = '$patientID'");
                
                if($data->num_rows > 0){
                    $row = $data->fetch_assoc();
                    $_SESSION['patient_id'] = $row['patient_id'];
                    $_SESSION['patient_name'] = $row['name'];
                    $_SESSION['patient_DOB'] = $row['DOB'];
                    $_SESSION['patient_email'] = $row['email'];
                    $_SESSION['patient_phone'] = $row['phone_number'];
                    $_SESSION['patient_weight'] = $row['weight'];
                    $_SESSION['patient_height'] = $row['height'];
                    $_SESSION['patient_gender'] = $row['gender'];
                    $_SESSION['patient_address'] = $row['address'];
                    $_SESSION['patient_allergy'] = $row['allergy'];
                }
                else{
                    echo"FAILED";
                }
        
        
        ?>
            <h2 > Name : <?php echo("{$_SESSION['name']}"); ?> </h2>
            <h2 > DOB : <?php echo("{$_SESSION['patient_DOB']}"); ?> </h2>
            <h2 > Email : <?php echo("{$_SESSION['patient_email']}"); ?> </h2>
            <h2 > Phone : <?php echo("{$_SESSION['patient_phone']}"); ?> </h2>
            <h2 > Weight(kg) : <?php echo("{$_SESSION['patient_weight']}"); ?> </h2>
            <h2 > Height(cm) : <?php echo("{$_SESSION['patient_height']}"); ?> </h2>
            <h2 > Gender : <?php echo("{$_SESSION['patient_gender']}"); ?> </h2>
            <h2 > Address : <?php echo("{$_SESSION['patient_address']}"); ?> </h2>
            <h2 > Allergy : <?php echo("{$_SESSION['patient_allergy']}"); ?> </h2>
        </div>
        <div class="container__patient_prescription">
            <input type="text" name="searchPrescirptionBox" class="search__prescription" id="searchPrescription"placeholder="Search prescription...">
            <button type="button" class="search__button--prescription" id="searchPrescriptionButton">Search</button>
            <table>
                <th>Date</th>
                <th>Prescription ID</th>
                <th>Requested By</th>
                <th>status</th>
                <th></th>
            <?php
                $connection = new mysqli('localhost', 'root', '','testestdb');
                $patient_id = $_SESSION['patient_id'];

                $data2 = $connection->query("SELECT * from prescription WHERE patient_id = $patient_id ORDER BY prescription.prescription_id DESC");
                $count = 0;
                if($data2->num_rows > 0){
                    while($row2 = $data2->fetch_assoc()){
                        $doctID = $row2['doctor_id'];
                        $data3 = $connection->query("SELECT * from user, doctor WHERE doctor.doctor_id = $doctID AND doctor.user_id = user.user_id");
                        while($row3 = $data3->fetch_assoc()){
                        echo "<tr><td>" . $row2['pr_date'] . "</td><td>" . $row2['token_string'] . "</td><td>Dr.".  $row3['name'] . "</td><td>". $row2['collection_status'] . "</td><td>".
                        "<button type='button' class='view__button--prescription' id='viewPrescriptionButton".$count."' value='".$row2['prescription_id']."'>View</button></td></tr>";
                        $count = $count + 1;
                        }
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