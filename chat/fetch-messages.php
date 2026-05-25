<?php

session_start();

include("../includes/db.php");

$match_id = (int)$_GET['match_id'];
$current_user = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

$query = "

SELECT chats.*, users.username

FROM chats

JOIN users
ON chats.sender_id = users.id

WHERE match_id = '$match_id'

ORDER BY sent_at ASC

";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)){

?>

<div class="chat-message <?php echo ((int)$row['sender_id'] === $current_user) ? 'sent' : 'received'; ?>">

    <strong>
        <?php echo htmlspecialchars($row['username']); ?>
    </strong>

    <p>
        <?php echo htmlspecialchars($row['message']); ?>
    </p>

</div>

<?php } ?>
