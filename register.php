<?php

include("includes/db.php");

$message = "";

if(isset($_POST['registerBtn'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check Empty Fields
    if(empty($username) || empty($email) || empty($password)){

        $message = "All fields are required!";

    }else{

        // Check Existing Email
        $checkEmail = "SELECT * FROM users WHERE email='$email'";
        $checkQuery = mysqli_query($conn, $checkEmail);

        if(mysqli_num_rows($checkQuery) > 0){

            $message = "Email already exists!";

        }else{

            // Hash Password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert User
            $insertQuery = "INSERT INTO users(username, email, password)
                            VALUES('$username', '$email', '$hashedPassword')";

            if(mysqli_query($conn, $insertQuery)){

                $message = "Registration Successful!";

            }else{

                $message = "Something went wrong!";

            }

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Secure Lost & Found</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container auth-container">

        <div class="form-box">

            <h1>Create Account</h1>
            <p>Join Secure Lost & Found System</p>

            <?php

            if($message != ""){
                echo "<div class='message'>$message</div>";
            }

            ?>

            <form action="" method="POST">

                <input type="text"
                       name="username"
                       placeholder="Username"
                       required>

                <input type="email"
                       name="email"
                       placeholder="Email Address"
                       required>

                <input type="password"
                       name="password"
                       placeholder="Password"
                       required>

                <button type="submit" name="registerBtn">
                    Register
                </button>

            </form>

            <div class="bottom-text">
                Already have an account?
                <a href="login.php">Login</a>
            </div>

        </div>

    </div>

</body>
</html>