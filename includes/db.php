<?php

// Current server ka hostname le lo
// $_SERVER['HTTP_HOST'] batata hai ke application kis domain par chal raha hai
$host = $_SERVER['HTTP_HOST'];

// Check karo ke application localhost par hai ya live server par
if ($host == 'localhost') {

    // ===== LOCAL XAMPP SETTINGS =====
    // Jab application apne computer par localhost par chal raha hai
    // Ye local database settings use karte hain
    $db_host = "localhost";           // Database server ka address
    $db_user = "root";                // Database username (default xampp username)
    $db_pass = "";                    // Database password (xampp mein khali hai)
    $db_name = "lostify";             // Database ka naam

} else {

    // ===== LIVE INFINITYFREE SETTINGS =====
    // Jab application live server par deployed hai
    // Ye live database settings use karte hain
    // InfinityFree ek free hosting service hai
    $db_host = "sql210.infinityfree.com";  // Live database server ka address
    $db_user = "if0_42014561";             // Live database username
    $db_pass = "VvqhA0WR6dEN";            // Live database password
    $db_name = "if0_42014561_lostify";    // Live database ka naam
}

// mysqli_connect() function se database se connection banao
// 4 parameters: host, username, password, database name
// Connection successful ho gaya to $conn mein connection object save hota hai
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check karo ke connection successful ho gaya ya nahi
// Agar connection fail ho to die() se error message dikhao
// mysqli_connect_error() exact error ka message deta hai
if(!$conn){
    // Agar database connect nahi hua to program yahi ruk jata hai
    die("Database connection failed: " . mysqli_connect_error());
}

?>
