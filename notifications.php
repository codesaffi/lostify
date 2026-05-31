<?php
// User ke session ko shuru karo aur zaroori files load karo
session_start();
include("includes/config.php");
include("includes/db.php");

// Check karo ke user login hai ya nahi
// Agar login nahi hai to login page par bhej do
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Current user ki ID nikal lo
$user_id = $_SESSION['user_id'];

// Database se is user ke sab notifications nikaal lo
// DESC matlab sabse nayi notifications pehle ayein
$query = "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Jab user notifications page ko kholata hai to sab notifications ko "read" mark kar do
// Iska matlab notifications padi ja chuki hain aur ab naye se notification nahi aa rahe
mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id='$user_id'");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=neon-mobilefix-2">
</head>
<body>
<?php include("includes/header.php"); ?>

<div class="notifications-container">
    <h1>Your Notifications</h1>

    <!-- Database se aaye notifications ko loop mein chalake har ek ko display karo -->
    <?php while($row = mysqli_fetch_assoc($result)){ ?>
        <div class="notification-card">
            <!-- Notification type ko display karo (pehla letter capital ho) -->
            <h3><?php echo ucfirst($row['type']); ?></h3>
            
            <!-- Notification ka message dikha do -->
            <p><?php echo $row['message']; ?></p>
            
            <!-- Kab ka notification aya tha ye likha -->
            <small><?php echo $row['created_at']; ?></small>
        </div>
    <?php } ?>
</div>

</body>
</html>
