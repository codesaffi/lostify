<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include("includes/header.php"); ?>

<div class="container">

    <div class="form-box">

        <h1>Welcome, <?php echo $_SESSION['username']; ?> 👋</h1>

        <p>You are logged in successfully.</p>

        <a href="logout.php">
            <button>Logout</button>
        </a>

    </div>

</div>

</body>
</html>