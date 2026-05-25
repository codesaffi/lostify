<?php

session_start();

include("../includes/db.php");

if(isset($_POST['message']) && isset($_SESSION['user_id'])){

    $match_id = (int)$_POST['match_id'];

    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sender_id = (int)$_SESSION['user_id'];

    $check = mysqli_query(
        $conn,
        "SELECT matches.id
         FROM matches
         JOIN lost_items ON matches.lost_item_id = lost_items.id
         JOIN found_items ON matches.found_item_id = found_items.id
         WHERE matches.id='$match_id'
         AND matches.status IN ('approved', 'location_ready')
         AND (lost_items.user_id='$sender_id' OR found_items.user_id='$sender_id')"
    );

    if(mysqli_num_rows($check) === 0){
        exit();
    }

    mysqli_query(
        $conn,

        "INSERT INTO chats
        (match_id, sender_id, message)
        VALUES
        ('$match_id', '$sender_id', '$message')"
    );

}
?>
