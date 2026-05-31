<?php

session_start();

include("../includes/config.php");
include("../includes/db.php");

// URL se match_id lo
$match_id = (int)$_GET['match_id'];

// Current logged-in user ka ID lo
$current_user = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Query: match ke sab messages nikalo with usernames
$query = "SELECT chats.*, users.username
          FROM chats
          JOIN users ON chats.sender_id = users.id
          WHERE match_id = '$match_id'
          ORDER BY sent_at ASC";

// Query execute karo
$result = mysqli_query($conn, $query);

// Har message ko loop se display karo
while($row = mysqli_fetch_assoc($result)){
?>

<!-- Check karo ke ye message khud ka hai ya second user ka -->
<!-- Agar khud ka hai to 'sent' class, nahi to 'received' class -->
<div class="chat-message <?php echo ((int)$row['sender_id'] === $current_user) ? 'sent' : 'received'; ?>">
    <strong>
        <?php echo htmlspecialchars($row['username']); ?>
    </strong>
    <p>
        <?php echo htmlspecialchars($row['message']); ?>
    </p>
</div>

<?php } ?>
