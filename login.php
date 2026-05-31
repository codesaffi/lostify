<?php
// Session start karo - ye user ke liye ek unique ID banata hai
// Browser band hone tak yeh session active rehta hai
session_start();
include("includes/config.php");
include("includes/db.php");

// Message variable - login ke baad success/error message show karega
$message = "";

// Default role user hai - jab tak admin select nahi ho
$selectedRole = "user";

// Email value ko form mein wapas dikhaane ke liye
$emailValue = "";

// Dekhna ke login button click hua hai
if(isset($_POST['loginBtn'])){

    // Form se email le lo aur extra spaces hatao
    $emailValue = trim($_POST['email'] ?? "");
    
    // SQL injection se bachne ke liye email ko escape karo
    // Dangerous characters ko safe banao
    $email = mysqli_real_escape_string($conn, $emailValue);
    
    // Form se password le lo
    $password = $_POST['password'] ?? "";
    
    // Dekhna ke user nay admin select kiya hai ya user
    // Ternary operator (? :) se ek line mein check kar sakte hain
    // Agar admin select hai to 'admin' set karo, warna 'user'
    $selectedRole = (isset($_POST['account_type']) && $_POST['account_type'] === 'admin') ? 'admin' : 'user';

    // Database se check karo ke is email se user mojood hai ya nahi
    // SELECT * matlab sab columns ko lao us user ke liye
    // WHERE email='$email' matlab sirf us email ke saath wale ko
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    // mysqli_num_rows() check karta hai ke query se kitne rows mili
    // Agar exactly 1 row mila to user database mein hai
    if(mysqli_num_rows($result) == 1){

        // mysqli_fetch_assoc() pura user data array ke form mein deta hai
        // Ab $user array mein id, username, password, role sab ho gaya
        $user = mysqli_fetch_assoc($result);

        // Password verify karo
        // password_verify() entered password ko hashed password se compare karta hai
        // Pehla argument jo user ne enter kiya, doosra argument database mein stored hai
        if(password_verify($password, $user['password'])){

            // Check karo ke agar admin select kiya hai to user actually admin ho
            // Agar user admin nahi hai lekin admin select kiya to error dikhao
            if($selectedRole === 'admin' && $user['role'] !== 'admin'){
                $message = "Admin access is only available for administrator accounts.";
            }else{
                // Session banao - yeh user ke liye browser mein data store karega
                // Taak session active rahe, user logged in rahe
                // $_SESSION array use karke data store karte hain
                $_SESSION['user_id'] = $user['id'];        // User ka unique ID
                $_SESSION['username'] = $user['username']; // User ka naam
                $_SESSION['role'] = $user['role'];         // User ka role (admin ya user)

                // Check karo ke user admin hai ya normal user
                // Admin ko admin dashboard par bhejo
                if($user['role'] === 'admin'){
                    // header() command se redirect karte hain
                    // Browser ko naya page load karne ko bolte hain
                    header("Location: admin/dashboard.php");
                }else{
                    // Normal user ko home page par bhejo
                    header("Location: index.php");
                }
                // exit() command se code yahin ruk jata hai
                // Age wale code nahi chal sakte
                exit();
            }

        }else{
            // Agar password sahi nahi hai to error message dikhao
            $message = "Incorrect password!";
        }

    }else{
        // Agar email database mein nahi mila to error message
        $message = "User not found!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Lostify</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css?v=neon-mobilefix-2">
</head>
<body class="auth-page">

<main class="auth-shell">
    <section class="auth-visual" aria-label="Lostify overview">
        <a class="auth-brand" href="<?php echo BASE_URL; ?>index.php">Lostify</a>

        <div class="auth-copy">
            <span class="auth-kicker">Secure lost and found</span>
            <h1>Bring people and misplaced items back together.</h1>
            <p>Review reports, track matches, manage conversations, and keep returns moving from one focused workspace.</p>
        </div>

        <div class="auth-highlights" aria-label="Platform highlights">
            <div>
                <strong>Live</strong>
                <span>match updates</span>
            </div>
            <div>
                <strong>Admin</strong>
                <span>review tools</span>
            </div>
            <div>
                <strong>Safe</strong>
                <span>return flow</span>
            </div>
        </div>
    </section>

    <section class="auth-card" aria-labelledby="loginTitle">
        <div class="auth-card-header">
            <span class="auth-eyebrow">Welcome back</span>
            <h2 id="loginTitle">Sign in to your account</h2>
            <p>Choose user or admin access before continuing.</p>
        </div>

        <!-- Agar koi error message hai to use display karo -->
        <?php if($message != ""): ?>
            <div class="message message-error" role="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Form - login ke liye data submit karega -->
        <!-- method="POST" matlab data securely bheja jata hai -->
        <form method="POST" class="auth-form">
            <!-- Role toggle - user ya admin kaunsa account use karna hai -->
            <!-- Radio buttons use hote hain jab ek option select karni ho -->
            <div class="role-toggle" aria-label="Account type">
                <!-- User account option -->
                <!-- checked = iska matlab ye default selected hai -->
                <input class="role-input"
                       type="radio"
                       id="roleUser"
                       name="account_type"
                       value="user"
                       <?php echo $selectedRole === 'user' ? 'checked' : ''; ?>>
                <label for="roleUser">User</label>

                <!-- Admin account option -->
                <!-- Agar pehle admin select tha to checked rahega -->
                <input class="role-input"
                       type="radio"
                       id="roleAdmin"
                       name="account_type"
                       value="admin"
                       <?php echo $selectedRole === 'admin' ? 'checked' : ''; ?>>
                <label for="roleAdmin">Admin</label>
            </div>

            <!-- Email input field -->
            <!-- htmlspecialchars() dangerous characters ko block karta hai -->
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
            <!-- type="password" matlab dots/asterisks ke roop mein dikhega -->
            <!-- autocomplete="current-password" taak browser remember kare -->
            <label class="field-group">
                <span>Password</span>
                <input type="password"
                       name="password"
                       placeholder="Enter your password"
                       autocomplete="current-password"
                       required>
            </label>

            <!-- Submit button - jab click ho to form POST method se submit hota hai -->
            <!-- name="loginBtn" se backend samajh jata hai ke login button click hua -->
            <button type="submit" name="loginBtn" class="auth-submit">Sign in</button>
        </form>

        <!-- Naya user? Register page par ja sakte hain -->
        <div class="bottom-text">
            Don't have an account? <a href="<?php echo BASE_URL; ?>register.php">Create one</a>
        </div>
    </section>
</main>

</body>
</html>
