<?php

// Session ka status check karo
// session_status() function batata hai ke session already start ho gaya ya nahi
// PHP_SESSION_NONE matlab session abhi shuru nahi hua
// Agar session nahi hai to shuru karo (agar pehle se start hai to dobara nahi karenge)
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Config file ko include karo
// Isme database connection aur important settings hain
// __DIR__ current file ka folder path deta hai (absolute path)
include(__DIR__ . "/config.php");

// ===== USER AUTHENTICATION CHECK =====
// Check karo ke user logged in hai ya nahi
// $_SESSION['user_id'] user ke liye unique ID hota hai
// Agar user_id set nahi hai to matlab user login nahi hai
if(!isset($_SESSION['user_id'])){
    // User login nahi hai to use login page par bhej do
    // header() command browser ko naya page load karti hai
    header("Location: ../login.php");
    // Exit karo - age wala code execute nahi hona chahiye
    exit();
}

// ===== ADMIN ROLE CHECK =====
// Check karo ke user admin hai ya normal user
// $_SESSION['role'] mein user ka role save hai (admin ya user)
// Agar user role 'admin' nahi hai to access deny karo
// != matlab 'not equal' - agar role admin ke barabar nahi hai
if($_SESSION['role'] != 'admin'){
    // Die command se page yahi ruk jata hai
    // Aur ye message show hota hai
    // Admin nahi ho to koi admin page access nahi kar sakta
    die("Access Denied!");
}

?>

