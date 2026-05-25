<?php

function calculateMatchScore($lostItem, $foundItem){

    $score = 0;

    // CATEGORY MATCH
    if(strtolower($lostItem['category']) == strtolower($foundItem['category'])){
        $score += 40;
    }

    // TITLE SIMILARITY
    similar_text(
        strtolower($lostItem['title']),
        strtolower($foundItem['title']),
        $titlePercent
    );

    $score += ($titlePercent * 0.25);

    // LOCATION SIMILARITY
    similar_text(
        strtolower($lostItem['last_seen_location']),
        strtolower($foundItem['found_location']),
        $locationPercent
    );

    $score += ($locationPercent * 0.20);

    // DESCRIPTION SIMILARITY
    similar_text(
        strtolower($lostItem['description']),
        strtolower($foundItem['description']),
        $descriptionPercent
    );

    $score += ($descriptionPercent * 0.15);

    return round($score, 2);
}

function createNotification($conn, $user_id, $message, $type){

    // Security
    $message = mysqli_real_escape_string($conn, $message);

    $type = mysqli_real_escape_string($conn, $type);

    // Insert notification
    mysqli_query(

        $conn,

        "INSERT INTO notifications
        (user_id, message, type)

        VALUES
        ('$user_id', '$message', '$type')"
    );
}

function rewardUser($conn, $user_id, $points){

    // GET CURRENT USER

    $query = mysqli_query(

        $conn,

        "SELECT points
         FROM users
         WHERE id='$user_id'"
    );

    $user = mysqli_fetch_assoc($query);

    $newPoints = $user['points'] + $points;

    // DETERMINE BADGE

    $badge = "Beginner Helper";

    if($newPoints >= 50){
        $badge = "Trusted Finder";
    }

    if($newPoints >= 150){
        $badge = "Community Hero";
    }

    if($newPoints >= 300){
        $badge = "Legend Rescuer";
    }

    // UPDATE USER

    mysqli_query(

        $conn,

        "UPDATE users

        SET
        points='$newPoints',
        reputation=reputation+1,
        badge='$badge'

        WHERE id='$user_id'"
    );
}

function columnExists($conn, $table, $column){

    $table = mysqli_real_escape_string($conn, $table);
    $column = mysqli_real_escape_string($conn, $column);

    $result = mysqli_query(
        $conn,
        "SHOW COLUMNS FROM `$table` LIKE '$column'"
    );

    return $result && mysqli_num_rows($result) > 0;
}

function markItemResolvedIfColumnExists($conn, $table, $item_id){

    $item_id = (int)$item_id;

    if(columnExists($conn, $table, "status")){
        mysqli_query(
            $conn,
            "UPDATE `$table`
             SET status='resolved'
             WHERE id='$item_id'"
        );
    }

    if(columnExists($conn, $table, "is_public")){
        mysqli_query(
            $conn,
            "UPDATE `$table`
             SET is_public=1
             WHERE id='$item_id'"
        );
    }
}

?>
