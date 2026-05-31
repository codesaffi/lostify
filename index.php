<?php

// Is page ka kaam hai resolved cases dikhana
// Yani wo matches jo items wapas mil chuke hain
include("includes/config.php");
include("includes/db.php");

// Database se returned status wali matches uthao
// aur lost/found items ki details saath join karo
$resolvedQuery = mysqli_query(
    $conn,
    "SELECT
        matches.id AS match_id,
        matches.match_score,
        matches.resolved_at,
        lost_items.title AS lost_title,
        lost_items.description AS lost_description,
        lost_items.category AS lost_category,
        lost_items.last_seen_location,
        lost_items.lost_date,
        lost_items.image AS lost_image,
        found_items.title AS found_title,
        found_items.description AS found_description,
        found_items.category AS found_category,
        found_items.found_location,
        found_items.found_date,
        found_items.image AS found_image,
        lost_user.username AS lost_username,
        found_user.username AS found_username
     FROM matches
     JOIN lost_items ON matches.lost_item_id = lost_items.id
     JOIN found_items ON matches.found_item_id = found_items.id
     JOIN users AS lost_user ON lost_items.user_id = lost_user.id
     JOIN users AS found_user ON found_items.user_id = found_user.id
     WHERE matches.status='returned'
     ORDER BY COALESCE(matches.resolved_at, matches.id) DESC"
);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Resolved Cases | Lostify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=neon-mobilefix-2">
</head>
<body>
<?php include("includes/header.php"); ?>

<div class="resolved-container">

    <div class="resolved-hero">
        <div>
            <h1>Resolved Cases</h1>
            <p>Successfully returned lost items from the Lostify community.</p>
        </div>

        <?php if(!isset($_SESSION['user_id'])){ ?>
            <div class="resolved-actions">
                <a href="login.php"><button>Login</button></a>
                <a href="register.php"><button class="secondary-button">Register</button></a>
            </div>
        <?php } ?>
    </div>

    <?php if(mysqli_num_rows($resolvedQuery) === 0){ ?>

        <div class="empty-chat-state large">
            No resolved cases yet.
        </div>

    <?php } ?>

    <div class="resolved-grid">

        <?php // Loop se har resolved case nikal ke card banayenge
        while($case = mysqli_fetch_assoc($resolvedQuery)){ 
            $lostImage = !empty($case['lost_image']) ? "assets/uploads/" . $case['lost_image'] : "";
            $foundImage = !empty($case['found_image']) ? "assets/uploads/" . $case['found_image'] : "";
        ?>

            <article class="resolved-card">

                <div class="resolved-card-header">
                    <span class="status-pill">Resolved</span>
                    <strong><?php echo htmlspecialchars($case['match_score']); ?>% match</strong>
                </div>

                <div class="resolved-pair">
                    <section>
                        <h2>Lost Report</h2>

                        <?php if($lostImage !== ""){ ?>
                            <img src="<?php echo htmlspecialchars($lostImage); ?>" alt="">
                        <?php } ?>

                        <h3><?php echo htmlspecialchars($case['lost_title']); ?></h3>
                        <p><?php echo htmlspecialchars($case['lost_description']); ?></p>
                        <small>
                            <?php echo htmlspecialchars($case['lost_category']); ?> |
                            Last seen at <?php echo htmlspecialchars($case['last_seen_location']); ?>
                        </small>
                    </section>

                    <section>
                        <h2>Found Report</h2>

                        <?php if($foundImage !== ""){ ?>
                            <img src="<?php echo htmlspecialchars($foundImage); ?>" alt="">
                        <?php } ?>

                        <h3><?php echo htmlspecialchars($case['found_title']); ?></h3>
                        <p><?php echo htmlspecialchars($case['found_description']); ?></p>
                        <small>
                            <?php echo htmlspecialchars($case['found_category']); ?> |
                            Found at <?php echo htmlspecialchars($case['found_location']); ?>
                        </small>
                    </section>
                </div>

                <div class="resolved-meta">
                    <span>Lost by @<?php echo htmlspecialchars($case['lost_username']); ?></span>
                    <span>Found by @<?php echo htmlspecialchars($case['found_username']); ?></span>
                    <?php if(!empty($case['resolved_at'])){ ?>
                        <span>Resolved <?php echo htmlspecialchars($case['resolved_at']); ?></span>
                    <?php } ?>
                </div>

            </article>

        <?php } ?>

    </div>

</div>

<?php include("includes/footer.php"); ?>
</body>
</html>
