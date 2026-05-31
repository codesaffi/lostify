<?php
// ===== SESSION INITIALIZATION =====
// Check karo ke session pehle se start ho chuka hai ya nahi
// session_status() function session ka status check karta hai
// PHP_SESSION_NONE matlab session abhi start nahi hua
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Config file ko include karo (BASE_URL define hota hai)
include(__DIR__ . "/config.php");

// ===== CHECK USER STATUS =====
// Check karo ke logged in user admin hai ya normal user
// isset() dekhta hai ke variable exist karta hai ya nahi
// $_SESSION['role'] user ka role rakhta hai
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Check karo ke user logged in hai ya nahi
// isset($_SESSION['user_id']) check karta hai ke user ID session mein hai ya nahi
$isLoggedIn = isset($_SESSION['user_id']);

// Username ko htmlspecialchars() ke sath display karo
// htmlspecialchars() dangerous characters ko block karta hai
// Agar user logged in nahi hai to 'Guest' show karo
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';

// ===== FETCH UNREAD NOTIFICATIONS COUNT =====
// Normal users ke liye unread notifications count nikalo
// Admin ko notifications dikhane ki zaroorat nahi hai
$unreadNotifications = 0;

// Sirf agar user logged in hai aur admin nahi hai
if($isLoggedIn && !$isAdmin){
    // Check karo ke database connection pehle se hai ya nahi
    // agar nahi hai to db.php include karo
    if(!isset($conn)){
        include(__DIR__ . "/db.php");
    }

    // Current logged in user ka ID le lo aur integer mein convert karo
    // (int) casting se string ko number mein badal dete hain
    // Ye security ke liye important hai
    $currentUserId = (int)$_SESSION['user_id'];
    
    // Database query - current user ke unread notifications count karo
    // COUNT(*) = total rows count karta hai
    // WHERE user_id = sirf us user ke notifications
    // is_read=0 = sirf unread notifications (padhe nahi gaye)
    $unreadQuery = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM notifications
         WHERE user_id='$currentUserId'
         AND is_read=0"
    );

    // Check karo ke query successful rahi ya nahi
    if($unreadQuery){
        // mysqli_fetch_assoc() result ko array mein convert karta hai
        // 'total' array ke key mein count save hota hai
        // (int) se string ko number mein convert karte hain
        $unreadNotifications = (int)mysqli_fetch_assoc($unreadQuery)['total'];
    }
}

// ===== NAVIGATION MENU SETUP =====
// User ke role ke hisaab se different navigation menu banana
// Admin, logged in user, ya guest - har kiska alag menu hota hai
$navLinks = [];

if($isAdmin){
    // Admin ka navigation menu
    // Admin ko dashboard, manage matches, etc dikhega
    $navLinks = [
        ['label' => 'Home', 'href' => BASE_URL . 'index.php'],
        ['label' => 'Dashboard', 'href' => BASE_URL . 'admin/dashboard.php'],
        ['label' => 'Manage Matches', 'href' => BASE_URL . 'admin/manage-matches.php'],
        ['label' => 'Logout', 'href' => BASE_URL . 'logout.php'],
    ];
} elseif($isLoggedIn) {
    // Normal logged in user ka navigation menu
    // User ko profile, dashboard, report, chat, notifications dikhega
    $navLinks = [
        ['label' => 'Home', 'href' => BASE_URL . 'index.php'],
        ['label' => 'Profile', 'href' => BASE_URL . 'profile.php'],
        ['label' => 'Dashboard', 'href' => BASE_URL . 'dashboard.php'],
        ['label' => 'Report Lost', 'href' => BASE_URL . 'user/report-lost.php'],
        ['label' => 'Report Found', 'href' => BASE_URL . 'user/report-found.php'],
        ['label' => 'Chat', 'href' => BASE_URL . 'chat/chat.php'],
        ['label' => 'Notifications', 'href' => BASE_URL . 'notifications.php'],
        ['label' => 'Logout', 'href' => BASE_URL . 'logout.php'],
    ];
} else {
    // Guest (logout/not logged in) ka navigation menu
    // Guest ko sirf home, login, register dikhega
    $navLinks = [
        ['label' => 'Home', 'href' => BASE_URL . 'index.php'],
        ['label' => 'Login', 'href' => BASE_URL . 'login.php'],
        ['label' => 'Register', 'href' => BASE_URL . 'register.php'],
    ];
}
?>

<!-- HTML Header Structure -->
<div class="site-shell">
    <!-- Site Header - Logo aur menu button -->
    <header class="site-header">
        <!-- Brand/Logo -->
        <div class="site-brand">
            <a href="<?php echo BASE_URL; ?>index.php">Lostify</a>
        </div>
        
        <!-- Mobile menu toggle button -->
        <!-- Mobile devices par ye hamburger icon show hota hai -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <!-- Header actions - Username greeting -->
        <div class="header-actions">
            <!-- Current logged in user ka naam show karo -->
            <span class="header-welcome">Hello, <?php echo $username; ?></span>
        </div>
    </header>

    <!-- Main Layout Container -->
    <div class="site-layout">
        <!-- Sidebar Navigation Menu -->
        <!-- Mobile par ye hamburger button se open/close hota hai -->
        <aside class="sidebar" id="mobileSidebar">
            <!-- Sidebar title - admin ya user ke hisaab se different -->
            <div class="sidebar-title"><?php echo $isAdmin ? 'Admin Navigation' : 'User Navigation'; ?></div>
            
            <!-- Navigation Links List -->
            <ul class="sidebar-menu">
                <!-- $navLinks array ke har link ko loop se display karo -->
                <?php foreach($navLinks as $link): ?>
                    <li>
                        <!-- Navigation link -->
                        <a href="<?php echo $link['href']; ?>">
                            <span><?php echo $link['label']; ?></span>

                            <!-- Notifications badge -->
                            <!-- Sirf notifications link par unread count show karo -->
                            <?php if($link['label'] === 'Notifications' && $unreadNotifications > 0): ?>
                                <!-- Red badge mein unread notifications count -->
                                <span class="notification-badge"><?php echo $unreadNotifications; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <!-- Page ka main content yaha display hota hai -->
        <main class="content-area">
