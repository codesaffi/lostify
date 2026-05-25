<?php

session_start();

include("../includes/auth.php");
include("../includes/db.php");

// TOTAL USERS
$users = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$totalUsers = mysqli_fetch_assoc($users)['total'];

// LOST ITEMS
$lost = mysqli_query($conn, "SELECT COUNT(*) AS total FROM lost_items");
$totalLost = mysqli_fetch_assoc($lost)['total'];

// FOUND ITEMS
$found = mysqli_query($conn, "SELECT COUNT(*) AS total FROM found_items");
$totalFound = mysqli_fetch_assoc($found)['total'];

// MATCHES
$matches = mysqli_query($conn, "SELECT COUNT(*) AS total FROM matches");
$totalMatches = mysqli_fetch_assoc($matches)['total'];

// RETURNED ITEMS
$returned = mysqli_query($conn, "SELECT COUNT(*) AS total FROM matches WHERE status='returned'");
$totalReturned = mysqli_fetch_assoc($returned)['total'];

?>

<!DOCTYPE html>
<html>
<head>

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="dashboard">

    <h1>📊 Admin Dashboard</h1>

    <div class="card-container">

        <div class="card">👤 Users: <?php echo $totalUsers; ?></div>

        <div class="card">📦 Lost Items: <?php echo $totalLost; ?></div>

        <div class="card">🔍 Found Items: <?php echo $totalFound; ?></div>

        <div class="card">🤝 Matches: <?php echo $totalMatches; ?></div>

        <div class="card">✅ Returned: <?php echo $totalReturned; ?></div>

    </div>

</div>

<?php include("../includes/footer.php"); ?>
</body>
</html>