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
if(isset($_POST['submitFound'])){

    // Form se data le lo aur database ke liye safe karo
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $date = $_POST['date'];
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // Image upload karna zaroori hai
    if(empty($_FILES['image']['name'])){
        $message = "Image is required!";
    } else {
        // File ka unique name banao (time + original name)
        $imageName = time() . "_" . $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        
        // File ko uploads folder mein move karo
        move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

        // Database mein found item insert karo
        $query = "INSERT INTO found_items
        (user_id, title, category, description, found_location, found_date, image, item_condition, additional_notes)
        VALUES
        ('$user_id', '$title', '$category', '$description', '$location', '$date', '$imageName', '$condition', '$notes')";

        // Matching code ko include karo
        include("../ajax/match-items.php");

        // Query execute karo
        if(mysqli_query($conn, $query)){

    // Naye inserted item ka ID lo
    $inserted_id = mysqli_insert_id($conn);

    // Found item ka data array banao matching ke liye
    $foundData = [
        'id' => $inserted_id,
        'title' => $title,
        'category' => $category,
        'found_location' => $location,
        'description' => $description
    ];

    // Sab lost items nikalo aur compare karo
    $lostQuery = mysqli_query($conn, "SELECT * FROM lost_items");

    // Har lost item ko compare karo
    while($lostItem = mysqli_fetch_assoc($lostQuery)){
        // Match score calculate karo
        $score = calculateMatchScore($lostItem, $foundData);

        // Agar 60% se zyada match hai
        if($score >= 60){
            // Check karo duplicate match naa ho
            $check = mysqli_query($conn,
                "SELECT * FROM matches
                 WHERE lost_item_id='{$lostItem['id']}'
                 AND found_item_id='$inserted_id'"
            );

            // Agar duplicate nahi hai to insert karo
            if(mysqli_num_rows($check) == 0){
                mysqli_query($conn,
                    "INSERT INTO matches
                    (lost_item_id, found_item_id, match_score)
                    VALUES
                    ('{$lostItem['id']}', '$inserted_id', '$score')"
                );
            }
        }
    }

    $message = "Found item reported successfully!";
}else{

            $message = "Something went wrong!";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Found Item</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css?v=neon-mobilefix-2">
</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="container">

    <div class="form-box">

        <h1>Report Found Item</h1>

        <?php
            if($message != ""){
                echo "<div class='message'>$message</div>";
            }
        ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text"
                   name="title"
                   placeholder="Item Title"
                   required>

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

            <textarea name="description"
                      placeholder="Describe the item"
                      required></textarea>

            <input type="text"
                   name="location"
                   placeholder="Found Location"
                   required>

            <input type="date"
                   name="date"
                   required>

            <input type="text"
                   name="condition"
                   placeholder="Item Condition">

            <textarea name="notes"
                      placeholder="Additional Notes"></textarea>

            <input type="file"
                   name="image"
                   required>

            <button type="submit" name="submitFound">
                Submit Found Report
            </button>

        </form>

    </div>

</div>

</body>
</html>
