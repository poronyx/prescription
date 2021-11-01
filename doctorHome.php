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
    if (isset($_POST['searched'])){
        $connection = new mysqli('localhost', 'root', '','testestdb');

        $search = $connection->real_escape_string($_POST['searchPHP']);

        $data = $connection->query("SELECT * FROM user,patient WHERE user.user_id = patient.user_id AND user.NRIC = '$search'");
        
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
            exit('found');
        }else{
            exit('failed');
        }
            
        exit($search);
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
            console.log('page ready: DoctorHome');
            $("#searchButton").on('click', function (){
                var search = $(".search__input").val();

                if(search == ""){
                    alert('Please fill in the values');
                }else{
                    $.ajax(
                    {
                        url: 'doctorHome.php',
                        method: 'POST',
                        data:{
                            searched: 1,
                            searchPHP: search
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('found') >= 0){

                                window.location = 'doctorHasPatient.php';
                                
                            }else{
                                alert("Search not found. Please try again");
                            }
                            

                            
                        },
                        dataType: 'text'
                    }
                );
                }
                console.log(search);

                
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
        <input type="text" name="searchBox" class="search__input" placeholder="Search for patient...">
        <button type="button" class="search__button" id="searchButton">Search</button>
    </div>
   
    
    
    
</body>
</html>