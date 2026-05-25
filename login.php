<?php
session_start();
include("includes/db.php");

$message = "";

if(isset($_POST['loginBtn'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){

        $user = mysqli_fetch_assoc($result);

        // Verify password
        if(password_verify($password, $user['password'])){

            // Create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] === 'admin'){
                header("Location: admin/dashboard.php");
            }else{
                header("Location: index.php");
            }
            exit();

        }else{
            $message = "Incorrect password!";
        }

    }else{
        $message = "User not found!";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Lostify</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container auth-container">

    <div class="form-box">

        <h1>Login</h1>
        <p>Access your Lost & Found account</p>

        <?php if($message != "") echo "<div class='message'>$message</div>"; ?>

        <form method="POST">

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="loginBtn">Login</button>

        </form>

        <div class="bottom-text">
            Don't have an account? <a href="register.php">Register</a>
        </div>

    </div>

</div>

</body>
</html>
