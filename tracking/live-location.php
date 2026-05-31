<?php

session_start();

include("../includes/config.php");
include("../includes/db.php");
include("../includes/functions.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['match_id'])){
    die("Invalid Match");
}

$match_id = (int)$_GET['match_id'];
$user_id = (int)$_SESSION['user_id'];
$message = "";

$matchQuery = mysqli_query(
    $conn,
    "SELECT
        matches.*,
        lost_items.id AS lost_item_id,
        lost_items.title AS lost_title,
        lost_items.description AS lost_description,
        lost_items.category AS lost_category,
        lost_items.last_seen_location,
        lost_items.image AS lost_image,
        found_items.id AS found_item_id,
        found_items.title AS found_title,
        found_items.description AS found_description,
        found_items.category AS found_category,
        found_items.found_location,
        found_items.image AS found_image,
        lost_items.user_id AS lost_user,
        found_items.user_id AS found_user,
        lost_user.username AS lost_username,
        found_user.username AS found_username
     FROM matches
     JOIN lost_items ON matches.lost_item_id = lost_items.id
     JOIN found_items ON matches.found_item_id = found_items.id
     JOIN users AS lost_user ON lost_items.user_id = lost_user.id
     JOIN users AS found_user ON found_items.user_id = found_user.id
     WHERE matches.id='$match_id'
     AND matches.status IN ('approved', 'returned')"
);

if(mysqli_num_rows($matchQuery) === 0){
    die("Match not found");
}

$match = mysqli_fetch_assoc($matchQuery);

if($user_id !== (int)$match['lost_user'] && $user_id !== (int)$match['found_user']){
    die("Access Denied");
}

if(isset($_POST['confirm_received']) && $match['status'] !== 'returned'){

    $confirmColumn = $user_id === (int)$match['lost_user'] ? "lost_confirmed_received" : "found_confirmed_received";

    mysqli_query(
        $conn,
        "UPDATE matches
         SET `$confirmColumn`=1
         WHERE id='$match_id'"
    );

    $matchQuery = mysqli_query(
        $conn,
        "SELECT *
         FROM matches
         WHERE id='$match_id'"
    );

    $updatedMatch = mysqli_fetch_assoc($matchQuery);

    if((int)$updatedMatch['lost_confirmed_received'] === 1 && (int)$updatedMatch['found_confirmed_received'] === 1){

        mysqli_query(
            $conn,
            "UPDATE matches
             SET status='returned',
                 resolved_at=NOW()
             WHERE id='$match_id'"
        );

        markItemResolvedIfColumnExists($conn, "lost_items", $match['lost_item_id']);
        markItemResolvedIfColumnExists($conn, "found_items", $match['found_item_id']);
        rewardUser($conn, $match['found_user'], 50);

        createNotification(
            $conn,
            $match['lost_user'],
            "Your item return was confirmed by both users. This case is now resolved.",
            "resolved"
        );

        createNotification(
            $conn,
            $match['found_user'],
            "Your found item return was confirmed. You earned 50 points.",
            "reward"
        );

        $message = "Both users confirmed. This case is now resolved.";
    }else{
        $message = "Your confirmation was saved. Waiting for the other user.";
    }

    $matchQuery = mysqli_query(
        $conn,
        "SELECT
            matches.*,
            lost_items.id AS lost_item_id,
            lost_items.title AS lost_title,
            lost_items.description AS lost_description,
            lost_items.category AS lost_category,
            lost_items.last_seen_location,
            lost_items.image AS lost_image,
            found_items.id AS found_item_id,
            found_items.title AS found_title,
            found_items.description AS found_description,
            found_items.category AS found_category,
            found_items.found_location,
            found_items.image AS found_image,
            lost_items.user_id AS lost_user,
            found_items.user_id AS found_user,
            lost_user.username AS lost_username,
            found_user.username AS found_username
         FROM matches
         JOIN lost_items ON matches.lost_item_id = lost_items.id
         JOIN found_items ON matches.found_item_id = found_items.id
         JOIN users AS lost_user ON lost_items.user_id = lost_user.id
         JOIN users AS found_user ON found_items.user_id = found_user.id
         WHERE matches.id='$match_id'"
    );

    $match = mysqli_fetch_assoc($matchQuery);
}

$currentUserConfirmed = $user_id === (int)$match['lost_user']
    ? (int)$match['lost_confirmed_received'] === 1
    : (int)$match['found_confirmed_received'] === 1;

?>

<!DOCTYPE html>
<html>
<head>

    <title>Live Location Tracking</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css?v=neon-mobilefix-2">
    <link rel="stylesheet"
href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<script src="https://unpkg.com/leaflet/dist/leaflet.js">
</script>

<script>
    // Set BASE_URL for JavaScript AJAX calls
    const BASE_URL_JS = "<?php echo BASE_URL; ?>";
</script>

</head>
<body>
<?php include("../includes/header.php"); ?>

<?php if($match['status'] === 'returned'){ 
    $lostImage = !empty($match['lost_image']) ? "../assets/uploads/" . $match['lost_image'] : "";
    $foundImage = !empty($match['found_image']) ? "../assets/uploads/" . $match['found_image'] : "";
?>

<div class="resolved-container">

    <?php if($message !== ""){ ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <article class="resolved-card">

        <div class="resolved-card-header">
            <span class="status-pill">Resolved</span>
            <strong><?php echo htmlspecialchars($match['match_score']); ?>% match</strong>
        </div>

        <div class="resolved-pair">
            <section>
                <h2>Lost Report</h2>

                <?php if($lostImage !== ""){ ?>
                    <img src="<?php echo htmlspecialchars($lostImage); ?>" alt="">
                <?php } ?>

                <h3><?php echo htmlspecialchars($match['lost_title']); ?></h3>
                <p><?php echo htmlspecialchars($match['lost_description']); ?></p>
                <small>
                    <?php echo htmlspecialchars($match['lost_category']); ?> |
                    Last seen at <?php echo htmlspecialchars($match['last_seen_location']); ?>
                </small>
            </section>

            <section>
                <h2>Found Report</h2>

                <?php if($foundImage !== ""){ ?>
                    <img src="<?php echo htmlspecialchars($foundImage); ?>" alt="">
                <?php } ?>

                <h3><?php echo htmlspecialchars($match['found_title']); ?></h3>
                <p><?php echo htmlspecialchars($match['found_description']); ?></p>
                <small>
                    <?php echo htmlspecialchars($match['found_category']); ?> |
                    Found at <?php echo htmlspecialchars($match['found_location']); ?>
                </small>
            </section>
        </div>

        <div class="resolved-meta">
            <span>Lost by @<?php echo htmlspecialchars($match['lost_username']); ?></span>
            <span>Found by @<?php echo htmlspecialchars($match['found_username']); ?></span>
            <?php if(!empty($match['resolved_at'])){ ?>
                <span>Resolved <?php echo htmlspecialchars($match['resolved_at']); ?></span>
            <?php } ?>
        </div>

    </article>

</div>

<?php include("../includes/footer.php"); ?>
</body>
</html>
<?php exit(); } ?>

<div class="tracking-container">

    <h1>Live Location Tracking</h1>

    <?php if($message !== ""){ ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <div class="location-card">

        <h2>Your Current Coordinates</h2>

        <p id="coords">
            Waiting for GPS...
        </p>

        <?php if($match['status'] !== 'returned'){ ?>
            <button onclick="shareLocation()">
                Share Live Location
            </button>
        <?php } else { ?>
            <span class="status-pill">Resolved</span>
        <?php } ?>

    </div>

    <div class="location-card">

        <h2>Tracked User Location</h2>

        <div id="map"></div>

    </div>

    <div class="location-card">

        <h2>Return Confirmation</h2>

        <p>
            Lost user confirmation:
            <strong><?php echo ((int)$match['lost_confirmed_received'] === 1) ? 'Done' : 'Pending'; ?></strong>
        </p>

        <p>
            Found user confirmation:
            <strong><?php echo ((int)$match['found_confirmed_received'] === 1) ? 'Done' : 'Pending'; ?></strong>
        </p>

        <?php if($match['status'] !== 'returned' && !$currentUserConfirmed){ ?>
            <form method="POST" class="confirm-return-form">
                <button type="submit" name="confirm_received">
                    I confirm the item was received
                </button>
            </form>
        <?php } elseif($match['status'] !== 'returned') { ?>
            <p class="muted-text">You already confirmed. Waiting for the other user.</p>
        <?php } else { ?>
            <p class="muted-text">Both users confirmed this case as resolved.</p>
        <?php } ?>

    </div>

</div>

<script>

const match_id = <?php echo $match_id; ?>;

let map = L.map('map').setView([33.6844, 73.0479], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {

    attribution: 'OpenStreetMap contributors'

}).addTo(map);

let marker = L.marker([33.6844, 73.0479]).addTo(map);

function shareLocation(){

    if(navigator.geolocation){

        navigator.geolocation.watchPosition(

            function(position){

                let lat = position.coords.latitude;

                let lng = position.coords.longitude;

                document.getElementById("coords").innerHTML =
                    `Latitude: ${lat}<br>Longitude: ${lng}`;

                fetch(BASE_URL_JS + "tracking/update-location.php", {

                    method: "POST",

                    headers:{
                        "Content-Type":"application/x-www-form-urlencoded"
                    },

                    body:
                    `match_id=${match_id}&latitude=${lat}&longitude=${lng}`

                });

            }

        );

    }else{

        alert("Geolocation not supported");

    }

}

function fetchLocation(){

    fetch(BASE_URL_JS + `tracking/fetch-location.php?match_id=${match_id}`)

    .then(response => response.json())

    .then(data => {

        if(data.latitude){

            let lat = parseFloat(data.latitude);

            let lng = parseFloat(data.longitude);

            marker.setLatLng([lat, lng]);

            map.setView([lat, lng], 15);

        }

    });

}

setInterval(fetchLocation, 3000);

fetchLocation();

</script>

</body>
<?php include("../includes/footer.php"); ?>
</html>
