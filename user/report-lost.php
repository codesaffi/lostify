<?php
session_start();
include("../includes/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

$message = "";

if(isset($_POST['submitLost'])){

    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $date = $_POST['date'];

    // Image upload (optional)
    $imageName = "";
    if(!empty($_FILES['image']['name'])){

        $imageName = time() . "_" . $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmp, "../assets/uploads/" . $imageName);
    }

    $query = "INSERT INTO lost_items 
    (user_id, title, category, description, last_seen_location, lost_date, image)
    VALUES 
    ('$user_id', '$title', '$category', '$description', '$location', '$date', '$imageName')";

    include("../ajax/match-items.php");

    if(mysqli_query($conn, $query)){
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="container">

    <div class="form-box">

        <h1>Report Lost Item</h1>

        <?php if($message != "") echo "<div class='message'>$message</div>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="title" placeholder="Item Title" required>

            <input type="text" name="category" placeholder="Category">

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