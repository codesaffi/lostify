<?php

session_start();

include("includes/db.php");

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch notifications before marking them as read

$query = "

SELECT *

FROM notifications

WHERE user_id='$user_id'

ORDER BY created_at DESC

";

$result = mysqli_query($conn, $query);

// Mark notifications as read after this page is opened

mysqli_query(

    $conn,

    "UPDATE notifications
     SET is_read=1
     WHERE user_id='$user_id'"
);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Notifications</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<?php include("includes/header.php"); ?>

<div class="notifications-container">

    <h1>Your Notifications</h1>

    <?php while($row = mysqli_fetch_assoc($result)){ ?>

        <div class="notification-card">

            <h3>

                <?php echo ucfirst($row['type']); ?>

            </h3>

            <p>

                <?php echo $row['message']; ?>

            </p>

            <small>

                <?php echo $row['created_at']; ?>

            </small>

        </div>

    <?php } ?>

</div>

</body>
</html>
