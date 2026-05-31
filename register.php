<?php

include("includes/config.php");
include("includes/db.php");

// Paigham aur error/success type ke liye variables tayyar karo
// Yeh variables khali strings se shuru hote hain
$message = "";
$messageType = "error";

// Form mein daale gaye username aur email ko store karne ke liye variables
// Agar user ko error aye to ye values form mein wapas dikhaenge
$usernameValue = "";
$emailValue = "";

// Dekhna ke user ne register button ko click kiya ya nahi
// Jab button click hoga tab yeh code chalega
if(isset($_POST['registerBtn'])){

    // Form se username, email, aur password le lo
    // trim() function extra spaces ko hatata hai (شروع aur آخر سے)
    // ?? matlab agar field khali ho to khali string le lo
    $usernameValue = trim($_POST['username'] ?? "");
    $emailValue = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    // SQL Injection se bachne ke liye special characters ko escape karo
    // Iska matlab dangerous characters ko normal banao
    // Jaise ' ko \'  mein badal do ta ke database ko problem na ho
    $username = mysqli_real_escape_string($conn, $usernameValue);
    $email = mysqli_real_escape_string($conn, $emailValue);

    // ===== VALIDATION CHECK 1 =====
    // Dekhna ke username, email aur password sab bhare hain ya nahi
    // Agar koi bhi field khali ho to error show karo
    if(empty($username) || empty($email) || empty($password)){

        $message = "All fields are required!";

    // ===== VALIDATION CHECK 2 =====
    // Email ka format check karo
    // filter_var() function dekhta hai ke email sahi format mein hai ya nahi
    // Jaise: abc@gmail.com format sahi hai but abc@gmail galat hai
    }elseif(!filter_var($emailValue, FILTER_VALIDATE_EMAIL)){

        $message = "Please enter a valid email address.";

    // ===== VALIDATION CHECK 3 =====
    // Password ki length check karo
    // strlen() function characters count karta hai
    // Password kam se kam 6 characters hona chahiye
    }elseif(strlen($password) < 6){

        $message = "Password must be at least 6 characters.";

    }else{
        // Sab validation pass ho gayi! Ab database mein check karo ke yeh email
        // pehle se exist karta hai ya nahi
        // SELECT command database se data maangta hai
        // WHERE clause mein jo email diye utne ko hi khoj kar lao
        $checkEmail = "SELECT * FROM users WHERE email='$email'";
        $checkQuery = mysqli_query($conn, $checkEmail);

        // mysqli_num_rows() check karta hai ke query se kitne rows mili
        // Agar 1 ya zyada rows hain to matlab yeh email pehle se hai
        if(mysqli_num_rows($checkQuery) > 0){

            $message = "Email already exists!";

        }else{
            // Email naya hai! Ab account banate hain

            // Password ko encrypt (hash) karo
            // password_hash() password ko ek alag roop mein badal deta hai
            // Iska faida ye hai ke original password kisi ko nazar nahi aata
            // PASSWORD_DEFAULT سب سے محفوظ method use karti hai
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Database mein naya user add karne ke liye query banao
            // INSERT command database mein nayi row add karta hai
            // VALUES mein hum username, email, hashed password, aur role dete hain
            $insertQuery = "INSERT INTO users(username, email, password, role)
                            VALUES('$username', '$email', '$hashedPassword', 'user')";

            // Ab query ko execute karo aur database mein data save karo
            if(mysqli_query($conn, $insertQuery)){

                // Agar query successfully chali to success message dikhao
                $message = "Registration successful. You can sign in now.";
                $messageType = "success";
                
                // Form ke fields ko khali kar do ta ke صاف نظر آئے
                $usernameValue = "";
                $emailValue = "";

            }else{
                // Agar query fail ho gayi to error message dikhao
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

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css?v=neon-mobilefix-2">
</head>
<body class="auth-page">

    <main class="auth-shell auth-shell-reverse">
        <section class="auth-visual" aria-label="Lostify overview">
            <a class="auth-brand" href="<?php echo BASE_URL; ?>index.php">Lostify</a>

            <div class="auth-copy">
                <span class="auth-kicker">Community powered</span>
                <h1>Report, match, chat, and return with confidence.</h1>
                <p>Create a user account to post lost or found items and follow each return from first report to final confirmation.</p>
            </div>

            <div class="auth-highlights" aria-label="Account highlights">
                <div>
                    <strong>Fast</strong>
                    <span>item reports</span>
                </div>
                <div>
                    <strong>Clear</strong>
                    <span>case status</span>
                </div>
                <div>
                    <strong>Direct</strong>
                    <span>owner chat</span>
                </div>
            </div>
        </section>

        <section class="auth-card" aria-labelledby="registerTitle">
            <div class="auth-card-header">
                <span class="auth-eyebrow">Get started</span>
                <h2 id="registerTitle">Create your account</h2>
                <p>New registrations are created as user accounts.</p>
            </div>

            <!-- Agar koi message hai (error ya success) to use display karo -->
            <?php if($message != ""): ?>
                <div class="message message-<?php echo $messageType; ?>" role="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Form jis mein user apna data daalta hai -->
            <!-- action=\"\" matlab yeh form apne aap ko submit karta hai (register.php ko) -->
            <!-- method=\"POST\" matlab data POST method se bheja jata hai (secure tarike se) -->
            <form action="" method="POST" class="auth-form">
                <!-- Username input field -->
                <!-- User apna username enter karega -->
                <label class="field-group">
                    <span>Username</span>
                    <input type="text"
                           name="username"
                           value="<?php echo htmlspecialchars($usernameValue); ?>"
                           placeholder="Choose a username"
                           autocomplete="username"
                           required>
                </label>

                <!-- Email input field -->
                <!-- type=\"email\" matlab sirf email format accept hoga -->
                <!-- htmlspecialchars() harmful characters ko block karta hai -->
                <label class="field-group">
                    <span>Email address</span>
                    <input type="email"
                           name="email"
                           value="<?php echo htmlspecialchars($emailValue); ?>"
                           placeholder="you@example.com"
                           autocomplete="email"
                           required>
                </label>

                <!-- Password input field -->
                <!-- type=\"password\" matlab password dot/asterisk ke roop mein dikhe -->
                <!-- minlength=\"6\" matlab kam se kam 6 characters hona chahiye -->
                <label class="field-group">
                    <span>Password</span>
                    <input type="password"
                           name="password"
                           placeholder="At least 6 characters"
                           autocomplete="new-password"
                           minlength="6"
                           required>
                </label>

                <div class="auth-note">
                    Need admin access? Sign in from the Admin option after an administrator creates your account.
                </div>

                <!-- Submit button - jab user is button ko click kare to form submit hota hai -->
                <!-- name="registerBtn" se backend code samajh jata hai ke register button click hua -->
                <button type="submit" name="registerBtn" class="auth-submit">
                    Create account
                </button>
            </form>

            <!-- Agar user ke paas pehle se account hai to login page par ja sakte hain -->
            <div class="bottom-text">
                Already have an account?
                <a href="<?php echo BASE_URL; ?>login.php">Sign in</a>
            </div>
        </section>
    </main>

</body>
</html>
