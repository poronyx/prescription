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
            unset($_SESSION['pres_id']);
            
            exit('back');
    }

    if (isset($_POST['edit'])){
        $_SESSION['edit_id'] = $_POST['editPresIDPHP']; 
        exit('editSuccess');
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
                        url: 'doctorViewPrescription.php',
                        method: 'POST',
                        data:{
                            back: 1
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('back') >= 0){

                                window.location = 'doctorHasPatient.php';
                                
                            }else{
                                alert("Please try again");
                            }
                            
                        },
                        dataType: 'text'
                    }
               );
            });

            $(".edit__button--prescription").on('click', function (event){  
                var buttonClicked = event.target.id ;  
                var editPresID = $("#"+buttonClicked).val();
                console.log(editPresID);
            $.ajax(
     {
         url: 'doctorViewPrescription.php',
         method: 'POST',
         data:{
             edit: 1,
             editPresIDPHP: editPresID
         },
         success: function(response){
             $("#response").html(response);

             if(response.indexOf('editSuccess') >= 0){

                window.location = 'doctorEditPrescription.php';
                 
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
        
        
        <div class="container__view_prescription">
            
            <table>
                <th>Dose</th>
                <th>Name</th>
                <th>Drug Prescription</th>
                <th>Frequency</th>
                <th>UOM</th>
                <th>Route</th>
                <th>Duration</th>
                <th> <th>
            <?php
                $connection = new mysqli('localhost', 'root', '','testestdb');
                $pres_id = $_SESSION['pres_id'];

                $data = $connection->query("SELECT * FROM prescriptionhasorder where prescription_id = '$pres_id'");
                
                if($data->num_rows > 0){
                    $count = 0;
                    while($row = $data->fetch_assoc()){
                        $medID = $row['medicine_id'];
                        
                        $data2 = $connection->query("SELECT * FROM medicine where medicine_id = '$medID'");
                        if($data2->num_rows > 0){
                        while($row2 = $data2->fetch_assoc()){
                            $data3 = $connection->query("SELECT * FROM prescription WHERE prescription_id = '$pres_id'");
                            if($data3->num_rows > 0){
                                while($row3 = $data3->fetch_assoc()){

                                echo "<tr><td>" . $row['dose'] ."</td><td>" . $row2['name'] .
                                "</td><td>" . $row['drug_prescription'] ."</td><td>". $row['frequency'] ."</td><td>" . $row['UOM'] ."</td><td>" .
                                $row['Route'] ."</td><td>" .$row['stop_after'] ."<td>";
                                    if($row3['collection_status'] === 'NOT COLLECTED' && $row3['doctor_id'] === $_SESSION['doctor_id']){
                                        echo "<button type='button' class='edit__button--prescription' id='editPrescriptionButton".$count
                                        ."' value='".$row['prescription_order_id']."'>Edit</button></td></td></tr>" ;
                                    }else{
                                        echo "</td></td></tr>" ;
                                    }
                                
                         
                                
                        $count = $count + 1;
                                }
                            }
                        }
                    }else{
                            echo"</table><br><h1>No Records</h1>";
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