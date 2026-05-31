<?php
// User ke session ko shuru karo aur configuration file load karo
session_start();
include("includes/config.php");

// Check karo ke user login hai ya nahi
// Agar login nahi hai to login page par bhej do
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?> 

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=neon-mobilefix-2">
</head>
<body>
<?php include("includes/header.php"); ?>

<div class="container">
    <div class="form-box">
        <!-- Welcome message dikha do - User ka naam session se nikaal kar likha -->
        <h1>Welcome, <?php echo $_SESSION['username']; ?> 👋</h1>

        <!-- User ko message do ke successfully login ho gaya -->
        <p>You are logged in successfully.</p>

        <!-- Logout button - Jab click karo to user logout ho jayga -->
        <a href="logout.php">
            <button>Logout</button>
        </a>
    </div>
</div>

</body>
</html>
