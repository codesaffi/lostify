<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

// Check Admin
if($_SESSION['role'] != 'admin'){
    die("Access Denied!");
}

?>