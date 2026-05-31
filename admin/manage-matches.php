<?php

include("../includes/config.php");
include("../includes/auth.php");
include("../includes/db.php");
include("../includes/functions.php");

// Approve Match
// Agar admin approve button click kare to yeh code chalta hai
if(isset($_GET['approve'])){

    $id = (int) $_GET['approve'];

    // Match status approve par set karo
    mysqli_query(
        $conn,
        "UPDATE matches
         SET status='approved'
         WHERE id='$id'"
    );

    // Ab lost aur found item ke owner user IDs lo
    $getUsers = mysqli_query(
        $conn,
        "SELECT
            lost_items.user_id AS lost_user,
            found_items.user_id AS found_user
         FROM matches
         JOIN lost_items ON matches.lost_item_id = lost_items.id
         JOIN found_items ON matches.found_item_id = found_items.id
         WHERE matches.id='$id'"
    );

    $users = mysqli_fetch_assoc($getUsers);

    // LOST USER NOTIFICATION

    createNotification(

        $conn,

        $users['lost_user'],

        "Admin approved your match. Chat is now enabled.",

        "approval"
    );

    // FOUND USER NOTIFICATION

    createNotification(

        $conn,

        $users['found_user'],

        "Admin approved your match. Chat is now enabled.",

        "approval"
    );

}

if(isset($_GET['returned'])){

    $id = (int) $_GET['returned'];

    // Jab item wapas mil jaye to match returned set karo
    mysqli_query(
        $conn,
        "UPDATE matches
         SET status='returned'
         WHERE id='$id'"
    );

    // Finder ka user_id hasil karo taake use reward aur notification mile
    $query = mysqli_query(
        $conn,
        "SELECT found_items.user_id AS finder
         FROM matches
         JOIN found_items ON matches.found_item_id = found_items.id
         WHERE matches.id='$id'"
    );

    $data = mysqli_fetch_assoc($query);

    // REWARD FINDER

    rewardUser(

        $conn,

        $data['finder'],

        50
    );

    // NOTIFICATION

    createNotification(

        $conn,

        $data['finder'],

        "Congratulations! You earned 50 points for returning an item.",

        "reward"
    );
}

// Reject Match
if(isset($_GET['reject'])){

    $id = (int) $_GET['reject'];

    mysqli_query(
        $conn,
        "UPDATE matches
         SET status='rejected'
         WHERE id='$id'"
    );
}

// Sabhi matches aur unke related lost/found item details nikalne ke liye query
$query = "

SELECT

matches.*,

lost_items.title AS lost_title,
lost_items.description AS lost_description,
lost_items.image AS lost_image,
lost_items.last_seen_location,
lost_items.lost_date,
lost_items.category AS lost_category,

found_items.title AS found_title,
found_items.description AS found_description,
found_items.image AS found_image,
found_items.found_location,
found_items.found_date,
found_items.category AS found_category,

lost_user.username AS lost_username,
found_user.username AS found_username

FROM matches

JOIN lost_items
ON matches.lost_item_id = lost_items.id

JOIN found_items
ON matches.found_item_id = found_items.id

JOIN users AS lost_user
ON lost_items.user_id = lost_user.id

JOIN users AS found_user
ON found_items.user_id = found_user.id

ORDER BY matches.match_score DESC

";

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Matches</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css?v=neon-mobilefix-2">

</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="matches-container">

    <h1 class="page-title">Smart Match Review Panel</h1>

    <?php while($row = mysqli_fetch_assoc($result)){ ?>

    <div class="match-card">

        <!-- LOST ITEM -->

        <div class="item-section">

            <div class="post-header">
                <h2>Lost Item</h2>
                <span>@<?php echo $row['lost_username']; ?></span>
            </div>

            <?php if(!empty($row['lost_image'])){ ?>

                <img src="../assets/uploads/<?php echo $row['lost_image']; ?>" class="item-image">

            <?php } ?>

            <div class="item-details">

                <h3><?php echo $row['lost_title']; ?></h3>

                <p><strong>Category:</strong> <?php echo $row['lost_category']; ?></p>

                <p><?php echo $row['lost_description']; ?></p>

                <p>
                    <strong>Last Seen:</strong>
                    <?php echo $row['last_seen_location']; ?>
                </p>

                <p>
                    <strong>Date:</strong>
                    <?php echo $row['lost_date']; ?>
                </p>

            </div>

        </div>

        <!-- FOUND ITEM -->

        <div class="item-section">

            <div class="post-header">
                <h2>Found Item</h2>
                <span>@<?php echo $row['found_username']; ?></span>
            </div>

            <?php if(!empty($row['found_image'])){ ?>

                <img src="../assets/uploads/<?php echo $row['found_image']; ?>" class="item-image">

            <?php } ?>

            <div class="item-details">

                <h3><?php echo $row['found_title']; ?></h3>

                <p><strong>Category:</strong> <?php echo $row['found_category']; ?></p>

                <p><?php echo $row['found_description']; ?></p>

                <p>
                    <strong>Found At:</strong>
                    <?php echo $row['found_location']; ?>
                </p>

                <p>
                    <strong>Date:</strong>
                    <?php echo $row['found_date']; ?>
                </p>

            </div>

        </div>

        <!-- MATCH INFO -->

        <div class="match-info">

            <h2><?php echo $row['match_score']; ?>% Match</h2>

            <p>Status: <?php echo $row['status']; ?></p>

            <div class="action-buttons">

                <a href="?approve=<?php echo $row['id']; ?>">
                    <button>Approve</button>
                </a>

                <a href="?reject=<?php echo $row['id']; ?>">
                    <button>Reject</button>
                </a>
                <a href="?returned=<?php echo $row['id']; ?>">
                    <button>Mark Returned</button>
                </a>
            </div>

        </div>

    </div>

    <?php } ?>

</div>

</body>
<?php include("../includes/footer.php"); ?>
</html>
