<?php
    session_start();

    unset($S_SESSION['loggedIN']);
    session_destroy();
    header('Location: login.php');
    exit();
?>