<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isLoggedIn = isset($_SESSION['user_id']);
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';
$unreadNotifications = 0;

if($isLoggedIn && !$isAdmin){
    if(!isset($conn)){
        include(__DIR__ . "/db.php");
    }

    $currentUserId = (int)$_SESSION['user_id'];
    $unreadQuery = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM notifications
         WHERE user_id='$currentUserId'
         AND is_read=0"
    );

    if($unreadQuery){
        $unreadNotifications = (int)mysqli_fetch_assoc($unreadQuery)['total'];
    }
}

$navLinks = [];

if($isAdmin){
    $navLinks = [
        ['label' => 'Home', 'href' => '/lostify/index.php'],
        ['label' => 'Dashboard', 'href' => '/lostify/admin/dashboard.php'],
        ['label' => 'Manage Matches', 'href' => '/lostify/admin/manage-matches.php'],
        ['label' => 'Logout', 'href' => '/lostify/logout.php'],
    ];
} elseif($isLoggedIn) {
    $navLinks = [
        ['label' => 'Home', 'href' => '/lostify/index.php'],
        ['label' => 'Profile', 'href' => '/lostify/profile.php'],
        ['label' => 'Dashboard', 'href' => '/lostify/dashboard.php'],
        ['label' => 'Report Lost', 'href' => '/lostify/user/report-lost.php'],
        ['label' => 'Report Found', 'href' => '/lostify/user/report-found.php'],
        ['label' => 'Chat', 'href' => '/lostify/chat/chat.php'],
        ['label' => 'Notifications', 'href' => '/lostify/notifications.php'],
        ['label' => 'Logout', 'href' => '/lostify/logout.php'],
    ];
} else {
    $navLinks = [
        ['label' => 'Home', 'href' => '/lostify/index.php'],
        ['label' => 'Login', 'href' => '/lostify/login.php'],
        ['label' => 'Register', 'href' => '/lostify/register.php'],
    ];
}
?>

<div class="site-shell">
    <header class="site-header">
        <div class="site-brand">
            <a href="/lostify/index.php">Lostify</a>
        </div>
        <div class="header-actions">
            <span class="header-welcome">Hello, <?php echo $username; ?></span>
        </div>
    </header>

    <div class="site-layout">
        <aside class="sidebar">
            <div class="sidebar-title"><?php echo $isAdmin ? 'Admin Navigation' : 'User Navigation'; ?></div>
            <ul class="sidebar-menu">
                <?php foreach($navLinks as $link): ?>
                    <li>
                        <a href="<?php echo $link['href']; ?>">
                            <span><?php echo $link['label']; ?></span>

                            <?php if($link['label'] === 'Notifications' && $unreadNotifications > 0): ?>
                                <span class="notification-badge"><?php echo $unreadNotifications; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="content-area">
