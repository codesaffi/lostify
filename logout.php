<?php
// Session ko start karo
// Taak user ke session data ko access kar saken
session_start();

// Configuration file ko include karo
// Isme database connection aur important settings hain
include("includes/config.php");

// Session ko destroy karo (khatam karo)
// Iska matlab $_SESSION array khali ho jata hai
// Ab user logged out ho gaya
// Browser mein stored session ID ab kaam nahi aata
session_destroy();

// User ko login page par redirect karo
// header() command browser ko naya page load karne ke liye bolti hai
// Location: login.php matlab login.php par le jao
header("Location: login.php");

// exit() command se pehla logout ho gaya, baaki code nahi chalega
// Ye ensure karta hai ke logout properly ho jaye
exit();
?>
