<?php
/**
 * Application Configuration
 * Determines BASE_URL based on current environment (localhost vs live server)
 */

// BASE_URL sirf ek bar define karo
// Agar pehle se define hai to dobara define nahi karega
if(!defined('BASE_URL')){
    // Check karo ke application localhost par chal raha hai ya live server par
    // $_SERVER['HTTP_HOST'] current website ka domain name deta hai
    // === matlab exactly equal (same value aur same type)
    // strpos() function text ke ander substring khojta hai
    $isLocalhost = $_SERVER['HTTP_HOST'] === 'localhost' || 
                   strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0 ||
                   strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === 0;

    // Agar localhost hai to /lostify/ path set karo
    // Agar live server hai to sirf / path set karo
    // define() function permanent constant banata hai
    // Constant ek bar define ho gaya to change nahi ho sakta
    if($isLocalhost){
        define('BASE_URL', '/lostify/');
    } else {
        define('BASE_URL', '/');
    }
}
?>
