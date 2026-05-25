<?php

session_start();

include("includes/db.php");

// GET TOP USERS

$query = mysqli_query(

    $conn,

    "SELECT username, points, reputation, badge

     FROM users

     ORDER BY points DESC

     LIMIT 10"
);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Leaderboard</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

<div class="leaderboard-container">

    <h1>🏆 Top Community Helpers</h1>

    <?php

    $rank = 1;

    while($row = mysqli_fetch_assoc($query)){

    ?>

        <div class="leaderboard-card">

            <div class="leaderboard-rank">

                #<?php echo $rank; ?>

            </div>

            <div class="leaderboard-info">

                <h2>
                    <?php echo $row['username']; ?>
                </h2>

                <p>
                    🏆 <?php echo $row['badge']; ?>
                </p>

            </div>

            <div class="leaderboard-stats">

                <h3>
                    ⭐ <?php echo $row['points']; ?>
                </h3>

                <small>
                    🔥 Reputation:
                    <?php echo $row['reputation']; ?>
                </small>

            </div>

        </div>

    <?php

        $rank++;

    }

    ?>

</div>

</body>
</html>
