<?php

session_start();

include("../includes/config.php");
include("../includes/db.php");
include("../includes/functions.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$selected_match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;
$selectedChat = null;

if(isset($_POST['start_location'])){

    $match_id = (int)$_POST['match_id'];

    mysqli_query(
        $conn,
        "UPDATE matches
         JOIN lost_items ON matches.lost_item_id = lost_items.id
         JOIN found_items ON matches.found_item_id = found_items.id
         SET matches.location_ready_at=IFNULL(matches.location_ready_at, NOW())
         WHERE matches.id='$match_id'
         AND matches.status IN ('approved', 'location_ready')
         AND (lost_items.user_id='$user_id' OR found_items.user_id='$user_id')"
    );

    header("Location: ../tracking/live-location.php?match_id=" . $match_id);
    exit();
}

$chatQuery = mysqli_query(
    $conn,
    "SELECT
        matches.*,
        lost_items.title AS lost_title,
        lost_items.image AS lost_image,
        lost_items.user_id AS lost_user,
        found_items.title AS found_title,
        found_items.image AS found_image,
        found_items.user_id AS found_user,
        lost_user.username AS lost_username,
        found_user.username AS found_username,
        latest.message AS last_message,
        latest.sent_at AS last_message_at
     FROM matches
     JOIN lost_items ON matches.lost_item_id = lost_items.id
     JOIN found_items ON matches.found_item_id = found_items.id
     JOIN users AS lost_user ON lost_items.user_id = lost_user.id
     JOIN users AS found_user ON found_items.user_id = found_user.id
     LEFT JOIN chats AS latest ON latest.id = (
        SELECT chats.id
        FROM chats
        WHERE chats.match_id = matches.id
        ORDER BY chats.sent_at DESC
        LIMIT 1
     )
     WHERE matches.status IN ('approved', 'location_ready', 'returned')
     AND (lost_items.user_id='$user_id' OR found_items.user_id='$user_id')
     ORDER BY COALESCE(latest.sent_at, matches.id) DESC"
);

$chats = [];

while($row = mysqli_fetch_assoc($chatQuery)){
    $chats[] = $row;

    if($selected_match_id === 0){
        $selected_match_id = (int)$row['id'];
    }

    if((int)$row['id'] === $selected_match_id){
        $selectedChat = $row;
    }
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Chats</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css?v=neon-mobilefix-2">

</head>
<body>
<script>
    // Set BASE_URL for JavaScript AJAX calls
    const BASE_URL_JS = "<?php echo BASE_URL; ?>";
</script>
<?php include("../includes/header.php"); ?>

<div class="chat-container chat-app">

    <aside class="chat-list">
        <div class="chat-list-header">
            <h1>Chats</h1>
            <span><?php echo count($chats); ?> active</span>
        </div>

        <?php if(count($chats) === 0){ ?>

            <div class="empty-chat-state">
                Approved matches will appear here when an admin enables chat.
            </div>

        <?php } ?>

        <?php foreach($chats as $chat){ 
            $isLostUser = $user_id === (int)$chat['lost_user'];
            $otherName = $isLostUser ? $chat['found_username'] : $chat['lost_username'];
            $itemTitle = $isLostUser ? $chat['found_title'] : $chat['lost_title'];
            $image = $isLostUser ? $chat['found_image'] : $chat['lost_image'];
            $imagePath = !empty($image) ? "../assets/uploads/" . $image : "../assets/images/default.png";
            $isActive = (int)$chat['id'] === $selected_match_id;
        ?>

            <a class="chat-list-item <?php echo $isActive ? 'active' : ''; ?>"
               href="<?php echo BASE_URL; ?>chat/chat.php?match_id=<?php echo $chat['id']; ?>">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="">
                <div>
                    <strong><?php echo htmlspecialchars($otherName); ?></strong>
                    <span><?php echo htmlspecialchars($itemTitle); ?></span>
                    <p><?php echo htmlspecialchars($chat['last_message'] ?: 'Start the conversation'); ?></p>
                </div>
            </a>

        <?php } ?>
    </aside>

    <section class="chat-thread">
        <?php if($selectedChat){ 
            $isLostUser = $user_id === (int)$selectedChat['lost_user'];
            $otherName = $isLostUser ? $selectedChat['found_username'] : $selectedChat['lost_username'];
            $itemTitle = $isLostUser ? $selectedChat['found_title'] : $selectedChat['lost_title'];
        ?>

            <div class="chat-header">
                <div>
                    <h2><?php echo htmlspecialchars($otherName); ?></h2>
                    <p><?php echo htmlspecialchars($itemTitle); ?></p>
                </div>

                <?php if($selectedChat['status'] !== 'returned'){ ?>
                    <form method="POST" class="chat-location-form">
                        <input type="hidden" name="match_id" value="<?php echo $selected_match_id; ?>">
                        <button type="submit" name="start_location">
                            Chat completed, share location
                        </button>
                    </form>
                <?php } else { ?>
                    <span class="status-pill">Resolved</span>
                <?php } ?>
            </div>

            <div class="chat-box" id="chatBox"></div>

            <?php if($selectedChat['status'] !== 'returned'){ ?>
                <form id="chatForm">

                    <input type="hidden"
                           id="match_id"
                           value="<?php echo $selected_match_id; ?>">

                    <input type="text"
                           id="message"
                           placeholder="Type message..."
                           autocomplete="off"
                           required>

                    <button type="submit">
                        Send
                    </button>

                </form>
            <?php } ?>

        <?php } else { ?>

            <div class="empty-chat-state large">
                No approved chats yet.
            </div>

        <?php } ?>
    </section>

</div>

<?php if($selectedChat){ ?>
<script>

const chatForm = document.getElementById("chatForm");

if(chatForm){
    chatForm.addEventListener("submit", function(e){

        e.preventDefault();

        let message = encodeURIComponent(document.getElementById("message").value);

        let match_id = encodeURIComponent(document.getElementById("match_id").value);

        fetch(BASE_URL_JS + "chat/send-message.php", {

            method: "POST",

            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },

            body: `message=${message}&match_id=${match_id}`

        })

        .then(response => response.text())

        .then(data => {

            document.getElementById("message").value = "";

            loadMessages();

        });

    });
}

function loadMessages(){

    let match_id = encodeURIComponent(<?php echo $selected_match_id; ?>);

    fetch(BASE_URL_JS + `chat/fetch-messages.php?match_id=${match_id}`)

    .then(response => response.text())

    .then(data => {

        const chatBox = document.getElementById("chatBox");
        chatBox.innerHTML = data;
        chatBox.scrollTop = chatBox.scrollHeight;

    });

}

setInterval(loadMessages, 2000);

loadMessages();

</script>
<?php } ?>

</body>
<?php include("../includes/footer.php"); ?>
</html>
