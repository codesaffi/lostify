<?php
session_start();

include("../includes/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

$message = "";

if(isset($_POST['submitFound'])){

    $user_id = $_SESSION['user_id'];

    $title = mysqli_real_escape_string($conn, $_POST['title']);

    $category = mysqli_real_escape_string($conn, $_POST['category']);

    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $location = mysqli_real_escape_string($conn, $_POST['location']);

    $date = $_POST['date'];

    $condition = mysqli_real_escape_string($conn, $_POST['condition']);

    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // Image REQUIRED
    if(empty($_FILES['image']['name'])){

        $message = "Image is required!";

    }else{

        $imageName = time() . "_" . $_FILES['image']['name'];

        $tmpName = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

        $query = "INSERT INTO found_items
        (user_id, title, category, description, found_location, found_date, image, item_condition, additional_notes)
        VALUES
        ('$user_id', '$title', '$category', '$description', '$location', '$date', '$imageName', '$condition', '$notes')";

        include("../ajax/match-items.php");

        if(mysqli_query($conn, $query)){

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

    <link rel="stylesheet" href="../assets/css/style.css">
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