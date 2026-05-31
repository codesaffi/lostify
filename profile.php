<?php
// User ke session ko shuru karo aur configuration files ko load karo
// Yeh zaruri files hein jo database aur app ke settings ke liye chahiye
session_start();
include("includes/config.php");
include("includes/db.php");

// Check karo ke user login ho gaya hai ya nahi
// Agar user logged in nahi hai to usko login page par bhej do
// Yeh security ke liye zaroori hai taaki koi bhi profile page ko directly access na kar sake
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Session se current user ki ID nikal lo
// Yeh ID database se user ka sahi data laane ke liye use hogi
$user_id = $_SESSION['user_id'];

// Database se user ki complete information le lo (sab kuch details)
// userQuery variable mein query ko store karte hain, phir fetch karke user variable mein data daal dete hain
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($userQuery);

// Message variable banao jo user ko upload ke baad success ya error message dikhayega
$message = "";

// Check karo ke user ne profile picture upload button daba hai ya nahi
// Jab button click hota hai to yeh condition true hoti hai
if(isset($_POST['uploadPic'])){
    // Pehle check karo ke file successfully upload ho gai hai aur koi error nahi hai
    // UPLOAD_ERR_OK matlab file properly upload hui hai
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
        $file = $_FILES['profile_pic'];
        
        // Sirf ye file types allow karo - jpg, jpeg, png, gif
        // Iska matlab sirf in format ki images upload ho sakti hain
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        // File ke actual type ko check karne ke liye MIME types define karo
        // Ye safe rakhta hai kyunke koi file ka extension change kar sakta hai
        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        
        // File ke extension (format) aur size ko nikal lo
        // pathinfo function se file name se extension mil jayega
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $maxSize = 3 * 1024 * 1024; // 3 MB se zyada file allow nahi hogi

        // File ki actual type check karo - sirf extension par na jakar real MIME type dekho
        // Yeh security ke liye hai taaki koi galat file na upload kar de
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // Dekhoo ke file type humari allowed list mein hai ya nahi
        // Agar nahi hai to error message dikhao
        if(!array_key_exists($mimeType, $allowedMimeTypes)){
            $message = "Only JPG, JPEG, PNG and GIF images are allowed.";
        }
        // Check karo ke file ki size 3MB se zyada to nahi hai
        // Agar zyada badi file ho to error message dikhao
        elseif($file['size'] > $maxSize){
            $message = "Image size must be 3MB or less.";
        }
        // Agar sab kuch theek hai to upload karna shuru karo
        else {
            // Agar extension galat hai to MIME type se sahi extension le lo
            // Yeh ensure karta hai ke file ka sahi format ho
            if(!in_array($fileExtension, $allowedExtensions)){
                $fileExtension = $allowedMimeTypes[$mimeType];
            }

            // File ko unique name de do taako server par conflict na ho
            // User ID + timestamp se har baar alag name ban jayega
            $newFileName = $user_id . '_' . time() . '.' . $fileExtension;
            $destination = __DIR__ . '/assets/uploads/' . $newFileName;

            // File ko uploads folder mein move karo
            // Temporary folder se permanent folder mein file le jaao
            if(move_uploaded_file($file['tmp_name'], $destination)){
                // Puraani profile picture ko server se delete karo
                // Agar pehle se koi picture the to us ko remove kar do taako space save ho
                if(!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'){
                    $oldPath = __DIR__ . '/assets/uploads/' . $user['profile_pic'];
                    if(file_exists($oldPath)){
                        @unlink($oldPath); // Puraani file ko delete kar do
                    }
                }

                // Database mein nayi profile picture ka naam save karo
                // Taako jab user phir se profile dekhey to nayi picture dikhe
                mysqli_query($conn, "UPDATE users SET profile_pic='$newFileName' WHERE id='$user_id'");

                $message = "Profile picture uploaded successfully.";

                // Database se phir se user ki updated information le lo
                // Taako nayi picture page par dikhay
                $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
                $user = mysqli_fetch_assoc($userQuery);
            } else {
                $message = "Failed to upload the image. Please try again.";
            }
        }
    } else {
        $message = "Please choose an image to upload.";
    }
}

// Is user ne jo lost items report kiye hain unko database se nikaal lo
// Order by: sabse nayi items pehle dikhai den
$lostQuery = mysqli_query($conn, "SELECT * FROM lost_items WHERE user_id='$user_id' ORDER BY created_at DESC");

// Is user ne jo found items report kiye hain unko database se nikaal lo
// Sabse nayi items ko top mein rakhta hai taako latest information pehle dikhe
$foundQuery = mysqli_query($conn, "SELECT * FROM found_items WHERE user_id='$user_id' ORDER BY created_at DESC");

// Database se un sab cases nikaal lo jo resolved ho chuke hain
// User ya to lost item reporter tha ya found item reporter tha in cases mein
// Ye dekhaata hai ke kaunsi items successfully return ho gai
$resolvedCaseQuery = mysqli_query($conn, "
    SELECT
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
    ORDER BY COALESCE(matches.resolved_at, matches.id) DESC
");

?>

<!DOCTYPE html>
<html>
<head>

    <title>My Profile</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/style.css?v=neon-mobilefix-2">

</head>
<body>
<?php include("includes/header.php"); ?>

<div class="profile-container">

    <!-- PROFILE HEADER - YE SECTION USER KI PROFILE KI JANKARI DIKHATA HAI -->
    <div class="profile-header">

        <?php
            // Default profile picture ka path set karo
            // Agar user ne picture upload nahi ki to default wali dikhe
            $profilePic = 'assets/images/default.png';
            
            // Agar user ne custom profile picture upload ki hai to use uski picture dikha
            if(!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'){
                $profilePic = 'assets/uploads/' . $user['profile_pic'];
            }
        ?>

        <img src="<?php echo $profilePic; ?>" class="profile-pic" alt="Profile Picture">

        <div class="profile-meta">
            <h1><?php echo $user['username']; ?></h1>
            <p><?php echo $user['email']; ?></p>

            <!-- Profile picture upload form - User picture upload karne ke liye -->
            <div class="profile-actions">
                <form action="" method="POST" enctype="multipart/form-data">
                    <label class="file-input-label">
                        <input type="file" name="profile_pic" accept="image/*">
                        Choose Image
                    </label>
                    <button type="submit" name="uploadPic">Upload</button>
                </form>
            </div>

            <!-- Agar upload mein message hai to dikha do (success ya error) -->
            <?php if($message !== ""): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
        </div>

        <!-- USER KE STATISTICS - POINTS, REPUTATION, BADGE KA SECTION -->
        <div class="profile-stats">
            <!-- Total Points - User ne kitne points earn kiye hain -->
            <div class="stat-box points-box">
                <h2>⭐ <?php echo $user['points']; ?></h2>
                <p>Total Points</p>
            </div>

            <!-- Reputation Score - User ki community reputation kitni hai -->
            <div class="stat-box reputation-box">
                <h2>🔥 <?php echo $user['reputation']; ?></h2>
                <p>Reputation</p>
            </div>

            <!-- Current Badge - User ko kaun sa badge mila hai -->
            <div class="stat-box badge-box">
                <h2>🏆 <?php echo $user['badge']; ?></h2>
                <p>Current Badge</p>
            </div>
        </div>

    </div>

    <!-- RESOLVED CASES SECTION - COMPLETED/SOLVED CASES DIKHANA HAI -->
    <!-- Yahan un cases ko show karte hain jahan lost aur found items successfully match ho gai -->
    <?php if(mysqli_num_rows($resolvedCaseQuery) > 0){ ?>

        <div class="history-section">
            <h2>Resolved Cases</h2>

            <div class="resolved-grid">
                <?php while($case = mysqli_fetch_assoc($resolvedCaseQuery)){ 
                    // Lost item ki image ka path banao
                    $lostImage = !empty($case['lost_image']) ? "assets/uploads/" . $case['lost_image'] : "";
                    // Found item ki image ka path banao
                    $foundImage = !empty($case['found_image']) ? "assets/uploads/" . $case['found_image'] : "";
                ?>

                    <article class="resolved-card">

                        <div class="resolved-card-header">
                            <span class="status-pill">Resolved</span>
                            <strong><?php echo htmlspecialchars($case['match_score']); ?>% match</strong>
                        </div>

                        <div class="resolved-pair">
                            <!-- Lost Item Ka Section - Jo chiz kho gayi thi -->
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

                            <!-- Found Item Ka Section - Jo chiz mil gai thi -->
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

    <!-- LOST ITEMS SECTION - USER NE JO ITEMS KHO JAN KA REPORT KIYA HAI -->
    <!-- Yeh sab lost items ko timeline format mein dikhata hai
    <div class="history-section">
        <h2>📦 Lost Items History</h2>

        <?php while($lost = mysqli_fetch_assoc($lostQuery)){ ?>

            <div class="history-card">
                <h3><?php echo $lost['title']; ?></h3>
                <p><?php echo $lost['description']; ?></p>
                <small><?php echo $lost['created_at']; ?></small>
            </div>

        <?php } ?>

    </div>

    <!-- FOUND ITEMS SECTION - USER NE JO ITEMS KHOJAY KA REPORT KIYA HAI -->
    <!-- Yeh sab found items ko timeline format mein dikhata hai
    <div class="history-section">
        <h2>🔍 Found Items History</h2>

        <?php while($found = mysqli_fetch_assoc($foundQuery)){ ?>

            <div class="history-card">
                <h3><?php echo $found['title']; ?></h3>
                <p><?php echo $found['description']; ?></p>
                <small><?php echo $found['created_at']; ?></small>
            </div>

        <?php } ?>

    </div>

</div>

<?php include("includes/footer.php"); ?>
</body>
</html>
