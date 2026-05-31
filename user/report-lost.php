<?php
session_start();
include("../includes/config.php");
include("../includes/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

$message = "";

// Jab form submit ho to yeh code chale
if(isset($_POST['submitLost'])){

    // Form se data le lo aur database ke liye safe karo
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $date = $_POST['date'];

    // Image optional hai
    $imageName = "";
    if(!empty($_FILES['image']['name'])){
        // File ka unique name banao
        $imageName = time() . "_" . $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        
        // File ko uploads folder mein move karo
        move_uploaded_file($tmp, "../assets/uploads/" . $imageName);
    }

    // Database mein lost item insert karo
    $query = "INSERT INTO lost_items 
    (user_id, title, category, description, last_seen_location, lost_date, image)
    VALUES 
    ('$user_id', '$title', '$category', '$description', '$location', '$date', '$imageName')";

    // Matching code ko include karo
    include("../ajax/match-items.php");

    // Query execute karo
    if(mysqli_query($conn, $query)){

    // Naye inserted item ka ID lo
    $inserted_id = mysqli_insert_id($conn);

    // Lost item ka data array banao matching ke liye
    $lostData = [
        'id' => $inserted_id,
        'title' => $title,
        'category' => $category,
        'last_seen_location' => $location,
        'description' => $description
    ];

    // Sab found items nikalo aur compare karo
    $foundQuery = mysqli_query($conn, "SELECT * FROM found_items");

    // Har found item ko compare karo
    while($foundItem = mysqli_fetch_assoc($foundQuery)){
        // Match score calculate karo
        $score = calculateMatchScore($lostData, $foundItem);

        // Agar 60% se zyada match hai
        if($score >= 60){
            // Check karo duplicate match naa ho
            $check = mysqli_query($conn,
                "SELECT * FROM matches
                 WHERE lost_item_id='$inserted_id'
                 AND found_item_id='{$foundItem['id']}'"
            );

            // Agar duplicate nahi hai to insert karo
            if(mysqli_num_rows($check) == 0){
                mysqli_query($conn,
                    "INSERT INTO matches
                    (lost_item_id, found_item_id, match_score)
                    VALUES
                    ('$inserted_id', '{$foundItem['id']}', '$score')"
                );
            }
        }
    }

    $message = "Lost item reported successfully!";
}else{
        $message = "Something went wrong!";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Lost Item</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css?v=neon-mobilefix-2">
</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="container">

    <div class="form-box">

        <h1>Report Lost Item</h1>

        <?php if($message != "") echo "<div class='message'>$message</div>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="title" placeholder="Item Title" required>

            <select name="category" required>

                <option value="">Select Category</option>

                <option value="Wallet">Wallet</option>
                <option value="Phone">Phone</option>
                <option value="Keys">Keys</option>
                <option value="ID Card">ID Card</option>
                <option value="Laptop">Laptop</option>
                <option value="Bag">Bag</option>
                <option value="Books">Books</option>
                <option value="Clothing">Clothing</option>
                <option value="Documents">Documents</option>
                <option value="Other">Other</option>

            </select>

            <textarea name="description" placeholder="Description" required></textarea>

            <input type="text" name="location" placeholder="Last Seen Location" required>

            <input type="date" name="date" required>

            <input type="file" name="image">

            <button type="submit" name="submitLost">Submit Report</button>

        </form>

    </div>

</div>

</body>
</html>
