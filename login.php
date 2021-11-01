<?php
    session_start();
    //alert function to tell user if wrong inputs
    //if user already logged in
    if (isset($_SESSION['loggedIN'])){
        if($_SESSION['userType'] == 'doctor'){
            header('Location: doctorHome.php');
        }else if($_SESSION['userType'] == 'patient'){
            header('Location: patientHome.php');
        }else if($_SESSION['userType'] == 'pharmacist'){
            header('Location: pharmacistHome.php');
        }
        else if($_SESSION['userType'] == 'admin'){
            header('Location: adminHome.php');
        }
        
        exit();
    }
    if (isset($_POST['login'])){
        $connection = new mysqli('localhost', 'root', '','testestdb');

        $username = $connection->real_escape_string($_POST['usernamePHP']);
        $password = $connection->real_escape_string($_POST['passwordPHP']);
        $userType = $connection->real_escape_string($_POST['userTypePHP']);

        $data = $connection->query("SELECT * FROM user WHERE nric ='$username' AND password='$password' AND role='$userType'");
        
        if($data->num_rows > 0){
            $row = $data->fetch_assoc();
            $_SESSION['loggedIN'] = '1';
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['userType'] = $userType;
            exit('success');
        }else{
            exit('failed');
        }
            
        exit($username . " = " . $password);
    }

    
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="css\login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            console.log('page ready');
            $("#loginButton").on('click', function (){
                var username = $("#username").val();
                var password = $("#password").val();
                var userType = $(".userType:checked").val();

                if(username == "" || password == ""){
                    alert('Please fill in the values');
                }else{
                    $.ajax(
                    {
                        url: 'login.php',
                        method: 'POST',
                        data:{
                            login: 1,
                            usernamePHP: username,
                            passwordPHP: password,
                            userTypePHP: userType
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('success') >= 0){
                                if(userType == "doctor"){
                                    window.location = 'doctorHome.php';
                                }else if(userType == "patient"){
                                    window.location = 'patientHome.php';
                                }else if(userType == "pharmacist"){
                                    window.location = 'pharmacistHome.php';
                                }else if(userType == "admin"){
                                    window.location = 'adminHome.php';
                                }
                            }else{
                                alert("Username/Password incorrect. Please try again");
                            }
                            

                            
                        },
                        dataType: 'text'
                    }
                );
                }
                console.log(username);
                console.log(password);
                console.log(userType);

                
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <form method="POST" >
            <div class="form__title">Login</div>
            <div class="form__input-group">
                <input type="text" class="form__input" autofocus placeholder="Username" name="username" id="username"> 
                
            </div>
            <div class="form__input-group">
                <input type="password" class="form__input" autofocus placeholder="Password" name="password" id="password">
                
            </div>
            <div id="form__radio">
                <input type="radio" id="r1" name="userType" class="userType" value="doctor" checked="checked"> Doctor
                <input type="radio" id="r2" name="userType" class="userType" value="patient"> Patient
                <input type="radio" id="r3" name="userType" class="userType" value="pharmacist" > Pharmacist 
                <input type="radio" id="r4" name="userType" class="userType" value="admin" > Admin  
            </div>

            <button id="loginButton" class="form__button" type="button">Continue</button>
        </form> 
    </div>
    
</body>
</html>