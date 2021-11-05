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
    if (isset($_POST['view'])){

        $_SESSION['pres_id'] = $_POST['viewPresIDPHP'];

        exit('viewSuccess');
    }
    if (isset($_POST['search'])){

        $connection = new mysqli('localhost', 'root', '','testestdb');
        $searchString = $_POST['searchPresPHP'];

        $data = $connection->query("SELECT * FROM prescription WHERE token_string = '$searchString' ");
        if($data->num_rows > 0){
          $row = $data->fetch_assoc();
          $patientID = $_SESSION['patient_id'];
          $patientDataID = $row['patient_id'];
          if($patientID."" === $patientDataID.""){
            $_SESSION['pres_id'] = $row['prescription_id'];
            exit('searchComplete');
          }else{
            exit($doctorID . "   " . $doctDataID);
          }
        }else{
            exit($searchString);
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

            $("#searchPrescriptionButton").on('click', function (){
                var searchPres = $("#searchPrescription").val();
               $.ajax(
                    {
                        url: 'doctorHasPatient.php',
                        method: 'POST',
                        data:{
                            search: 1,
                            searchPresPHP: searchPres
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('searchComplete') >= 0){

                                window.location = 'doctorViewPrescription.php';
                                
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
         url: 'doctorHasPatient.php',
         method: 'POST',
         data:{
             view: 1,
             viewPresIDPHP: viewPresID
         },
         success: function(response){
             $("#response").html(response);

             if(response.indexOf('viewSuccess') >= 0){

                window.location = 'doctorViewPrescription.php';
                 
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
        <img src="img\doctor.png" alt="doctor" style="width:7%">
        <div class="nav__name"><h2>Dr. <?php echo("{$_SESSION['name']}"); ?></h2></div>
        <ul>
            <li><a href="logout.php">Log out</a></li>
        </ul>
    </nav>

    <button type="button" class="back__button" id="backButton">Back</button>
    <div class="container">
        

        <div class="container__patient_details">
            
            <h2 > Name : <?php echo("{$_SESSION['patient_name']}"); ?> </h2>
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
            <button type="button" class="add__button--prescription" id="addPrescriptionButton">Add Prescription</button>
            <input type="text" name="searchPrescirptionBox" class="search__prescription" id="searchPrescription"placeholder="Search prescription...">
            <button type="button" class="search__button--prescription" id="searchPrescriptionButton">Search</button>
            <table>
                <th>Date</th>
                <th>Prescription ID</th>
                <th>Requested By</th>
                <th></th>
            <?php
                $connection = new mysqli('localhost', 'root', '','testestdb');
                $patient_id = $_SESSION['patient_id'];

                $data = $connection->query("SELECT * from prescription WHERE patient_id = $patient_id ORDER BY prescription.prescription_id DESC");
                $count = 0;
                if($data->num_rows > 0){
                    
                    while($row = $data->fetch_assoc()){
                        $doctID = $row['doctor_id'];
                        $data2 = $connection->query("SELECT * from user, doctor WHERE doctor.doctor_id = $doctID AND doctor.user_id = user.user_id");
                        while($row2 = $data2->fetch_assoc()){
                        echo "<tr><td>" . $row['pr_date'] . "</td><td>" . $row['token_string'] . "</td><td>Dr.".  $row2['name'] . "</td><td>".
                        "<button type='button' class='view__button--prescription' id='viewPrescriptionButton".$count."' value='".$row['prescription_id']."'>View</button></td></tr>";
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