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
    if (isset($_POST['backToPatientPHP'])){

      unset($_SESSION['dataArray']);

      exit('backToPatient');
}
    //generate a random token of strings
    function generateTokenString(){
      $tokenLength = 25;
      $string = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFEHIJKLMNOPQRSTUVWXYZ";
      $randString = substr(str_shuffle($string),0,$tokenLength);
      $randString = $randString . date("Ymd");
      return $randString;
    }

    function sendMail($patientName,$tokenString,$email): void{
      $to = $email;
      $subject = "Token String for prescription";
      $txt = "Hello " . $patientName .", your prescription string is:\n". $tokenString;
      $headers = "From: theintern2021@yahoo.com" ;
      mail($to,$subject,$txt,$headers);
    }

    if(!isset($_SESSION['dataArray'])){

      $_SESSION['dataArray'] = array();
    }

    $stringDate = "" . date("Y-m-d");
    $createToken = generateTokenString();
    

    if (isset($_POST['drugNamePHP'])){

        $connection = new mysqli('localhost', 'root', '','testestdb');

        $drugName = strtolower($connection->real_escape_string($_POST['drugNamePHP']));

        $data = $connection->query("SELECT * FROM medicine WHERE name = '$drugName'");
        
        if($data->num_rows > 0){
            global $medID;
            $row = $data->fetch_assoc();
            $_SESSION['med_id'] = $row['medicine_id'];
            unset($_POST['drugNamePHP']);
            exit('found');
        }else{
            unset($_POST['drugNamePHP']);
            exit('failed');
        }

        
      }
      

      if (isset($_POST['submitPHP'])){

        $drugID = $_SESSION['med_id']; 
        $drugPres = $_POST['drugPrescriptionPHP'];
        $drugDose = $_POST['drugDosePHP'];
        $drugFrequency = $_POST['drugFrequencyPHP'];
        $drugUOM = $_POST['drugUOMPHP'];
        $drugRoute = $_POST['drugRoutePHP'];
        $drugDuration = $_POST['drugDurationPHP'];

        array_push($_SESSION['dataArray'], array($drugID,$drugDose,$drugUOM,$drugFrequency,$drugRoute,$drugDuration,$drugPres));

        unset($_POST['submitPHP']);
        exit('complete');
        
      }

      if(isset($_POST['drugSubmit'])){

        $connection = new mysqli('localhost', 'root', '','testestdb');
        //get doctor_id
        $userID = $_SESSION['user_id'];
        $dataDoct = $connection->query("SELECT * FROM doctor WHERE user_id = '$userID'");
        if($dataDoct->num_rows > 0){
          $rowDoct = $dataDoct->fetch_assoc();
          $doctorID = $rowDoct['doctor_id'];
        }
         
        $patientID = $_SESSION['patient_id'];
        if($connection->query("INSERT INTO prescription(prescription_id, doctor_id, patient_id, pr_date, token_string, collection_status)
        VALUES (NULL,$doctorID,$patientID,STR_TO_DATE('$stringDate','%Y-%m-%d'),'$createToken','NOT COLLECTED')") === TRUE)
        {
          $data = $connection->query("SELECT * FROM prescription WHERE token_string = '$createToken'");

          $dataMedArrayP = $_SESSION['dataArray'];
          //get prescription_id from the one we just created
          if($data->num_rows > 0){
              $row = $data->fetch_assoc();
              $presID = $row['prescription_id'];
          }else{
              exit('failed');
          }
          foreach($_SESSION['dataArray'] as $key=>$value){
            $medIDSUBMIT = $value[0];
            $doseSUBMIT = $value[1];
            $UOMSUBMIT = $value[2];
            $freqSUBMIT = $value[3];
            $routeSUBMIT = $value[4];
            $durationSUBMIT = $value[5];
            $drugPresSUBMIT = $value[6];

            if($connection->query("INSERT INTO prescriptionhasorder(prescription_order_id, prescription_id, medicine_id, drug_prescription, quantity, dose, frequency, UOM, Route, stop_after) 
              VALUES (NULL,$presID,$medIDSUBMIT,'$drugPresSUBMIT',0,$doseSUBMIT,
              '$freqSUBMIT','$UOMSUBMIT','$routeSUBMIT','$durationSUBMIT')") === TRUE){

              }else{
                exit('no exist' . sizeof($_SESSION['dataArray']));
              }

          }
          sendMail($_SESSION['patient_name'],$createToken,$_SESSION['patient_email']);
          unset($_SESSION['dataArray']);
          exit('submitted');
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

    <button type="button" class="back__button" id="backButton" >Back</button>
    <?php

      
      
    ?>
    <div class="container">
        <div class="container__add_prescription">
            <button type="button" class="add__button--medicine" id="addButtonModal">Add Medicine</button>
            <table class = "table__add">
                <th>Name</th>
                <th>Dose</th>
                <th>UOM</th>
                <th>Frequency</th>
                <th>Route</th>
                <th>Duration</th> 
                <th>Special instruction</th>
                <tr>
                    <td><input class = "td__box" id ="td1" disabled></input></td><td><input class = "td__box" id ="td2" disabled></input></td>
                    <td><input class = "td__box" id ="td3" disabled></input></td><td><input class = "td__box" id ="td4" disabled></input></td>
                    <td><input class = "td__box" id ="td5" disabled></input></td><td><input class = "td__box" id ="td6" disabled></input></td>
                    <td><input class = "td__box" id ="td7" disabled></input></td>
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td8" disabled></input></td>
                    <td><input class = "td__box" id ="td9" disabled></input></td><td><input class = "td__box" id ="td10" disabled></input></td>
                    <td><input class = "td__box" id ="td11" disabled></input></td><td><input class = "td__box" id ="td12" disabled></input></td>
                    <td><input class = "td__box" id ="td13" disabled></input></td><td><input class = "td__box" id ="td14" disabled></input></td>
                    
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td15" disabled></input></td><td><input class = "td__box" id ="td16" disabled></input></td>
                    <td><input class = "td__box" id ="td17" disabled></input></td><td><input class = "td__box" id ="td18" disabled></input></td>
                    <td><input class = "td__box" id ="td19" disabled></input></td><td><input class = "td__box" id ="td20" disabled></input></td>
                    <td><input class = "td__box" id ="td21" disabled></input></td>
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td22" disabled></input></td>
                    <td><input class = "td__box" id ="td23" disabled></input></td><td><input class = "td__box" id ="td24" disabled></input></td>
                    <td><input class = "td__box" id ="td25" disabled></input></td><td><input class = "td__box" id ="td26" disabled></input></td>
                    <td><input class = "td__box" id ="td27" disabled></input></td><td><input class = "td__box" id ="td28" disabled></input></td>
                    
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td29" disabled></input></td><td><input class = "td__box" id ="td30" disabled></input></td>
                    <td><input class = "td__box" id ="td31" disabled></input></td><td><input class = "td__box" id ="td32" disabled></input></td>
                    <td><input class = "td__box" id ="td33" disabled></input></td><td><input class = "td__box" id ="td34" disabled></input></td>
                    <td><input class = "td__box" id ="td35" disabled></input></td>
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td36" disabled></input></td>
                    <td><input class = "td__box" id ="td37" disabled></input></td><td><input class = "td__box" id ="td38" disabled></input></td>
                    <td><input class = "td__box" id ="td39" disabled></input></td><td><input class = "td__box" id ="td40" disabled></input></td>
                    <td><input class = "td__box" id ="td41" disabled></input></td><td><input class = "td__box" id ="td42" disabled></input></td>
                    
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td43" disabled></input></td><td><input class = "td__box" id ="td44" disabled></input></td>
                    <td><input class = "td__box" id ="td45" disabled></input></td><td><input class = "td__box" id ="td46" disabled></input></td>
                    <td><input class = "td__box" id ="td47" disabled></input></td><td><input class = "td__box" id ="td48" disabled></input></td>
                    <td><input class = "td__box" id ="td49" disabled></input></td>
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td50" disabled></input></td>
                    <td><input class = "td__box" id ="td51" disabled></input></td><td><input class = "td__box" id ="td52" disabled></input></td>
                    <td><input class = "td__box" id ="td53" disabled></input></td><td><input class = "td__box" id ="td54" disabled></input></td>
                    <td><input class = "td__box" id ="td55" disabled></input></td><td><input class = "td__box" id ="td56" disabled></input></td>
                    
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td57" disabled></input></td><td><input class = "td__box" id ="td58" disabled></input></td>
                    <td><input class = "td__box" id ="td59" disabled></input></td><td><input class = "td__box" id ="td60" disabled></input></td>
                    <td><input class = "td__box" id ="td61" disabled></input></td><td><input class = "td__box" id ="td62" disabled></input></td>
                    <td><input class = "td__box" id ="td63" disabled></input></td>
                </tr>
                <tr>
                    <td><input class = "td__box" id ="td64" disabled></input></td>
                    <td><input class = "td__box" id ="td65" disabled></input></td><td><input class = "td__box" id ="td66" disabled></input></td>
                    <td><input class = "td__box" id ="td67" disabled></input></td><td><input class = "td__box" id ="td68" disabled></input></td>
                    <td><input class = "td__box" id ="td69" disabled></input></td><td><input class = "td__box" id ="td70" disabled></input></td>
                </tr>  
            </table>
            <br>
            <button type="button" class="add__button--medicine" id="submitPresButton">Submit Prescription</button>
        </div>
    </div>

    <div id="my-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close">&times;</span>
        <h2>Add Medicine</h2>
      </div>
      <div class="modal-body">
        <div class="box1">
            <div class="input__Text">Name : </div>
            <div class="input__Text">Dose : </div>
            <div class="input__Text">UOM : </div>
            <div class="input__Text">Frequency : </div>
            <div class="input__Text">Route : </div>
            <div class="input__Text">Duration : </div>
            <div class="input__Text">Special instruction : </div>
        </div>
        <div class="box2">
            <form method="POST">    
              <input type="text" name="medicineBox" class="medicine__input" id="drug--name"required="required">
              <input type="number" min ="1" step="1" pattern="\d+" name="medicineBox" class="medicine__input" id="drug--dose"required="required">
              <input type="text" name="medicineBox" class="medicine__input" id="drug--UOM"required="required">
              <input type="text" name="medicineBox" class="medicine__input" id="drug--frequency"required="required">
              <input type="text" name="medicineBox" class="medicine__input" id="drug--route"required="required">
              <input type="text" name="medicineBox" class="medicine__input" id="drug--duration"required="required">
              <input type="text" name="medicineBox" class="medicine__input" id="drug--prescription"required="required">
            </form>
        </div>
      </div>
      <div class="modal-footer">
        <button id="verifyButton" class="medicine__button" type="button">Verify</button>
        <button id="submitButton" class="medicine__button" type="button" disabled>Submit</button>
      </div>
    </div>
  </div>

  <script>

    $(document).ready(function () {    

      // Get the modal
      const modal = document.querySelector('#my-modal');
      const modalBtn = document.querySelector('#addButtonModal');
      const closeBtn = document.querySelector('.close');

      

    // Events
    modalBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', outsideClick);

// Open
    function openModal() {
      modal.style.display = 'block';
    }

// Close
    function closeModal() {
      modal.style.display = 'none';
    }

// Close If Outside Click
    function outsideClick(e) {
      if (e.target == modal) {
        modal.style.display = 'none';
      }
    }
    console.log('page ready: DoctorHasPatient');
    var count = 0;
    var pos1 = 1;
    var pos2 = 2;
    var pos3 = 3;
    var pos4 = 4;
    var pos5 = 5;
    var pos6 = 6;
    var pos7 = 7;
    var pos8 = 8;

    $("#backButton").on('click', function (){

$.ajax(
     {
         url: 'doctorAddPres.php',
         method: 'POST',
         data:{
          backToPatientPHP: 1
         },
         success: function(response){
             $("#response").html(response);

             if(response.indexOf('backToPatient') >= 0){

                 window.location = 'doctorHasPatient.php';
                 
             }else{
                 alert("Please try again");
             }
             

             
         },
         dataType: 'text'
     }
);
});
            $("#verifyButton").on('click', function (){
              var drugName = $("#drug--name").val();
              var drugDose = $("#drug--dose").val();
              var drugPrescription = $("#drug--prescription").val();
              var drugFrequency = $("#drug--frequency").val();
              var drugUOM = $("#drug--UOM").val();
              var drugRoute = $("#drug--route").val();
              var drugDuration = $("#drug--duration").val();


              if(drugName == "" || drugDose == "" || drugPrescription == "" || drugFrequency == "" || drugUOM == "" || drugRoute == "" || drugDuration == ""){
                alert('Please Fill in all the details');
              }else{
                $.ajax(
                    {
                        url: 'doctorAddPres.php',
                        method: 'POST',
                        data:{
                            drugNamePHP: drugName
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('found') >= 0){
                                document.getElementById("drug--name").disabled = true;
                                document.getElementById("drug--dose").disabled = true;
                                document.getElementById("drug--prescription").disabled = true;
                                document.getElementById("drug--frequency").disabled = true;
                                document.getElementById("drug--UOM").disabled = true;
                                document.getElementById("drug--route").disabled = true;
                                document.getElementById("drug--duration").disabled = true;
                                document.getElementById("submitButton").disabled = false;
                                alert("Verified!! Please click on submit button");
                                
                            }else{
                                alert("Drug name doesn't exist!");
                            }
                        },
                        dataType: 'text'
                    }
               );
              }
            });

            $("#submitPresButton").on('click', function (){

              //check if medicine not yet added
              if(document.getElementById("td1").value == "" ){
                alert('Please add at least 1 Medicine first!!');
              }else{
                $.ajax(
                    {
                        url: 'doctorAddPres.php',
                        method: 'POST',
                        data:{
                            drugSubmit: 1
                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('submitted') >= 0){
                                
                                alert("Prescription Successfully created");
                                window.location = 'doctorHasPatient.php';
                            }else{
                                alert("Failed!");
                            }
                        },
                        dataType: 'text'
                    }
               );
              }
            });

            $("#submitButton").on('click', function (){
              
              var drugName = $("#drug--name").val();
              var drugDose = $("#drug--dose").val();
              var drugPrescription = $("#drug--prescription").val();
              var drugFrequency = $("#drug--frequency").val();
              var drugUOM = $("#drug--UOM").val();
              var drugRoute = $("#drug--route").val();
              var drugDuration = $("#drug--duration").val();

              $.ajax(
                    {
                        url: 'doctorAddPres.php',
                        method: 'POST',
                        data:{
                            submitPHP: 1,
                            drugDosePHP: drugDose,
                            drugPrescriptionPHP: drugPrescription,
                            drugFrequencyPHP: drugFrequency,
                            drugUOMPHP: drugUOM,
                            drugRoutePHP: drugRoute,
                            drugDurationPHP: drugDuration

                        },
                        success: function(response){
                            $("#response").html(response);

                            if(response.indexOf('complete') >= 0){

                                
                                document.getElementById("drug--name").disabled = false;
                                document.getElementById("drug--dose").disabled = false;
                                document.getElementById("drug--prescription").disabled = false;
                                document.getElementById("drug--frequency").disabled = false;
                                document.getElementById("drug--UOM").disabled = false;
                                document.getElementById("drug--route").disabled = false;
                                document.getElementById("drug--duration").disabled = false;
                                // reset values
                                document.getElementById("drug--name").value = "";
                                document.getElementById("drug--dose").value = "";
                                document.getElementById("drug--prescription").value = "";
                                document.getElementById("drug--frequency").value = "";
                                document.getElementById("drug--UOM").value = "";
                                document.getElementById("drug--route").value = "";
                                document.getElementById("drug--duration").value = "";
                                document.getElementById("submitButton").disabled = true;
                                alert("Submitted successfully!!");
                                if(count == 0){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else{
                                  pos1 = pos1 + 7;
                                  pos2 = pos2 + 7;
                                  pos3 = pos3 + 7;
                                  pos4 = pos4 + 7;
                                  pos5 = pos5 + 7;
                                  pos6 = pos6 + 7;
                                  pos7 = pos7 + 7;
                                if(count == 1){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 2){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 3){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 4){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 5){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 6){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 7){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 8){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }else if(count == 9){
                                  document.getElementById("td" + pos1).value = drugDose;
                                  document.getElementById("td" + pos2).value = drugUOM;
                                  document.getElementById("td" + pos3).value = drugName;
                                  document.getElementById("td" + pos4).value = drugFrequency;
                                  document.getElementById("td" + pos5).value = drugRoute;
                                  document.getElementById("td" + pos6).value = drugDuration;
                                  document.getElementById("td" + pos7).value = drugPrescription;
                                  count = count + 1;
                                }
                              }
                                
                                
                            }else{
                                alert("Submission Failed");
                            }
                        },
                        dataType: 'text'
                    }
               );
              

            });
  });

  </script>



</body>
</html>