<?php

session_start();

include("../includes/config.php");
include("../includes/auth.php");
include("../includes/db.php");

// Config, auth aur database connection ready karte hain
// Yeh file sirf admin ke liye hai isliye auth zaroori hai

// Helper function for count queries
function getTotal($conn, $sql){
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)['total'];
}

// Total users
$totalUsers = getTotal($conn, "SELECT COUNT(*) AS total FROM users");

// Lost items
$totalLost = getTotal($conn, "SELECT COUNT(*) AS total FROM lost_items");

// Found items
$totalFound = getTotal($conn, "SELECT COUNT(*) AS total FROM found_items");

// Matches
$totalMatches = getTotal($conn, "SELECT COUNT(*) AS total FROM matches");

// Returned items
$totalReturned = getTotal($conn, "SELECT COUNT(*) AS total FROM matches WHERE status='returned'");

?>

<!DOCTYPE html>
<html>
<head>

    <title>Admin Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css?v=neon-mobilefix-2">

</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="dashboard">

    <!-- Dashboard page title -->
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
