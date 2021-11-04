
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href=".\css\test.css">
</head>
<body>
<button id="modal-btn" class="button">Click Here</button>
<?php
  
  $to = "por_-_@hotmail.com";
  $subject = "Token String for prescription";
  $txt = "Hello " . $patientName .", your prescription string is:/n". $tokenString;
  $headers = "From: theintern2021@yahoo.com" ;
  //mail($to,$subject,$txt,$headers);

  function sendMail($patientName,$tokenString,$email): void{
    $to = $email;
    $subject = "Token String for prescription";
    $txt = "Hello " . $patientName .", your prescription string is:\n". $tokenString;
    $headers = "From: theintern2021@yahoo.com" ;
    mail($to,$subject,$txt,$headers);
  }

  sendMail("Somphon","hahahaha","por_-_@hotmail.com");

  $dataArray = array();

  array_push($dataArray, array(123,3213,412));

  $_SESSION['array'] = array();

  array_push($_SESSION['array'], array(512515,51253,4));
  array_push($_SESSION['array'], array(515,5515153,12124));

  $newArray = $_SESSION['array'];


  foreach($newArray as $key=>$value){
    echo($value[0] . '<br>');
    echo($value[1] . '<br>');
    echo($value[2] . '<br>');
  }
  
  echo(sizeof($newArray). " hi <br>");
  for ($x = 0; $x < sizeof($dataArray); $x++) {
    echo($dataArray[$x][0] . "<br>");
    echo($dataArray[$x][1]. "<br>");
    echo($dataArray[$x][2]. "<br>");
  }
  $todayDate = date('Y-m-d');
  $stringDate = strtotime($todayDate);

  echo(gettype($stringDate));
  function generateTokenString(){
    $tokenLength = 25;
    $string = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFEHIJKLMNOPQRSTUVWXYZ";
    $randString = substr(str_shuffle($string),0,$tokenLength);
    $randString = $randString . date("Ymd");
    return $randString;
  }
  echo("<br>");
  echo(generateTokenString());
?>

  <div id="my-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close">&times;</span>
        <h2>Modal Header</h2>
      </div>
      <div class="modal-body">
        <p>This is my modal</p>
        <input type="text" name="searchBox" class="medicine__input" placeholder="Search for patient...">
      <input type="text" name="searchBox" class="medicine__input" placeholder="Search for patient...">
      <input type="text" name="searchBox" class="medicine__input" placeholder="Search for patient...">
      <input type="text" name="searchBox" class="medicine__input" placeholder="Search for patient...">
      <input type="text" name="searchBox" class="medicine__input" placeholder="Search for patient...">
      </div>
      <div class="modal-footer">
        <h3>Modal Footer</h3>
      </div>
    </div>
  </div>
<script>
// Get the modal
const modal = document.querySelector('#my-modal');
const modalBtn = document.querySelector('#modal-btn');
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
</script>

    
</body>
</html>