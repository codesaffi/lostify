<?php
// User ke session ko shuru karo aur zaroori files load karo
session_start();
include("includes/config.php");
include("includes/db.php");

// Database se top 10 users nikaal lo jo sabse zyada points rakhte hain
// DESC matlab highest points wale user pehle aayein
// LIMIT 10 matlab sirf top 10 users dikhao
$query = mysqli_query($conn, 
    "SELECT username, points, reputation, badge FROM users ORDER BY points DESC LIMIT 10"
);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=neon-mobilefix-2">
</head>
<body>

<div class="leaderboard-container">
    <h1>🏆 Top Community Helpers</h1>

    <?php
        // Rank variable ko 1 se shuru karo
        // Ye track karega ke har user ka kaunsa rank hai
        $rank = 1;
        
        // Database se aaye har user ko loop mein chalao
        while($row = mysqli_fetch_assoc($query)){
    ?>
        <div class="leaderboard-card">
            <!-- User ka rank dikha do -->
            <div class="leaderboard-rank">
                #<?php echo $rank; ?>
            </div>

            <!-- User ka naam aur badge dikha do -->
            <div class="leaderboard-info">
                <h2><?php echo $row['username']; ?></h2>
                <p>🏆 <?php echo $row['badge']; ?></p>
            </div>

            <!-- User ke points aur reputation ko dikha do -->
            <div class="leaderboard-stats">
                <h3>⭐ <?php echo $row['points']; ?></h3>
                <small>🔥 Reputation: <?php echo $row['reputation']; ?></small>
            </div>
        </div>

    <?php
        // Rank ko 1 se badhao taako next user ka rank alag ho
        $rank++;
    }
    ?>
</div>

</body>
</html>
