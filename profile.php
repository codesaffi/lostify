<?php

session_start();

include("includes/db.php");

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit();
}

$user_id = $_SESSION['user_id'];

// USER DATA

$userQuery = mysqli_query(

    $conn,

    "SELECT *
     FROM users
     WHERE id='$user_id'"
);

$user = mysqli_fetch_assoc($userQuery);

$message = "";

if(isset($_POST['uploadPic'])){
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
        $file = $_FILES['profile_pic'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $maxSize = 3 * 1024 * 1024; // 3 MB

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if(!array_key_exists($mimeType, $allowedMimeTypes)){
            $message = "Only JPG, JPEG, PNG and GIF images are allowed.";
        } elseif($file['size'] > $maxSize){
            $message = "Image size must be 3MB or less.";
        } else {
            if(!in_array($fileExtension, $allowedExtensions)){
                $fileExtension = $allowedMimeTypes[$mimeType];
            }

            $newFileName = $user_id . '_' . time() . '.' . $fileExtension;
            $destination = __DIR__ . '/assets/uploads/' . $newFileName;

            if(move_uploaded_file($file['tmp_name'], $destination)){
                if(!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'){
                    $oldPath = __DIR__ . '/assets/uploads/' . $user['profile_pic'];
                    if(file_exists($oldPath)){
                        @unlink($oldPath);
                    }
                }

                mysqli_query(
                    $conn,
                    "UPDATE users SET profile_pic='$newFileName' WHERE id='$user_id'"
                );

                $message = "Profile picture uploaded successfully.";

                $userQuery = mysqli_query(
                    $conn,
                    "SELECT * FROM users WHERE id='$user_id'"
                );

                $user = mysqli_fetch_assoc($userQuery);
            } else {
                $message = "Failed to upload the image. Please try again.";
            }
        }
    } else {
        $message = "Please choose an image to upload.";
    }
}

// LOST ITEMS

$lostQuery = mysqli_query(

    $conn,

    "SELECT *
     FROM lost_items
     WHERE user_id='$user_id'
     ORDER BY created_at DESC"
);

// FOUND ITEMS

$foundQuery = mysqli_query(

    $conn,

    "SELECT *
     FROM found_items
     WHERE user_id='$user_id'
     ORDER BY created_at DESC"
);

// RESOLVED CASES INVOLVING THIS USER

$resolvedCaseQuery = mysqli_query(

    $conn,

    "SELECT
        matches.id AS match_id,
        matches.match_score,
        matches.resolved_at,
        lost_items.title AS lost_title,
        lost_items.description AS lost_description,
        lost_items.category AS lost_category,
        lost_items.last_seen_location,
        lost_items.image AS lost_image,
        found_items.title AS found_title,
        found_items.description AS found_description,
        found_items.category AS found_category,
        found_items.found_location,
        found_items.image AS found_image,
        lost_user.username AS lost_username,
        found_user.username AS found_username
     FROM matches
     JOIN lost_items ON matches.lost_item_id = lost_items.id
     JOIN found_items ON matches.found_item_id = found_items.id
     JOIN users AS lost_user ON lost_items.user_id = lost_user.id
     JOIN users AS found_user ON found_items.user_id = found_user.id
     WHERE matches.status='returned'
     AND (lost_items.user_id='$user_id' OR found_items.user_id='$user_id')
     ORDER BY COALESCE(matches.resolved_at, matches.id) DESC"
);

?>

<!DOCTYPE html>
<html>
<head>

    <title>My Profile</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<?php include("includes/header.php"); ?>

<div class="profile-container">

    <!-- PROFILE HEADER -->

    <div class="profile-header">

        <?php
            $profilePic = 'assets/images/default.png';
            if(!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'){
                $profilePic = 'assets/uploads/' . $user['profile_pic'];
            }
        ?>

        <img src="<?php echo $profilePic; ?>" class="profile-pic" alt="Profile Picture">

        <div class="profile-meta">
            <h1><?php echo $user['username']; ?></h1>
            <p><?php echo $user['email']; ?></p>

            <div class="profile-actions">
                <form action="" method="POST" enctype="multipart/form-data">
                    <label class="file-input-label">
                        <input type="file" name="profile_pic" accept="image/*">
                        Choose Image
                    </label>
                    <button type="submit" name="uploadPic">Upload</button>
                </form>
            </div>

            <?php if($message !== ""): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
        </div>

        <div class="profile-stats">

<div class="stat-box points-box">

    <h2>
        ⭐ <?php echo $user['points']; ?>
    </h2>

    <p>Total Points</p>

</div>

<div class="stat-box reputation-box">

    <h2>
        🔥 <?php echo $user['reputation']; ?>
    </h2>

    <p>Reputation</p>

</div>

<div class="stat-box badge-box">

    <h2>
        🏆 <?php echo $user['badge']; ?>
    </h2>

    <p>Current Badge</p>

</div>

        </div>

    </div>

    <!-- RESOLVED CASES -->

    <?php if(mysqli_num_rows($resolvedCaseQuery) > 0){ ?>

        <div class="history-section">

            <h2>Resolved Cases</h2>

            <div class="resolved-grid">

                <?php while($case = mysqli_fetch_assoc($resolvedCaseQuery)){ 
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

    <?php } ?>

    <!-- LOST ITEMS -->

    <div class="history-section">

        <h2>📦 Lost Items History</h2>

        <?php while($lost = mysqli_fetch_assoc($lostQuery)){ ?>

            <div class="history-card">

                <h3>
                    <?php echo $lost['title']; ?>
                </h3>

                <p>
                    <?php echo $lost['description']; ?>
                </p>

                <small>
                    <?php echo $lost['created_at']; ?>
                </small>

            </div>

        <?php } ?>

    </div>

    <!-- FOUND ITEMS -->

    <div class="history-section">

        <h2>🔍 Found Items History</h2>

        <?php while($found = mysqli_fetch_assoc($foundQuery)){ ?>

            <div class="history-card">

                <h3>
                    <?php echo $found['title']; ?>
                </h3>

                <p>
                    <?php echo $found['description']; ?>
                </p>

                <small>
                    <?php echo $found['created_at']; ?>
                </small>

            </div>

        <?php } ?>

    </div>

</div>

<?php include("includes/footer.php"); ?>
</body>
</html>
