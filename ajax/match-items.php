<?php

include("../includes/config.php");
include("../includes/db.php");
include("../includes/functions.php");

// Get all lost items
$lostItems = [];
$lostQuery = mysqli_query($conn, "SELECT * FROM lost_items");
while($row = mysqli_fetch_assoc($lostQuery)){
    $lostItems[] = $row;
}

// Get all found items once
$foundItems = [];
$foundQuery = mysqli_query($conn, "SELECT * FROM found_items");
while($row = mysqli_fetch_assoc($foundQuery)){
    $foundItems[] = $row;
}

// Ab dono arrays ko ek doosre se compare karenge
// Har lost item ko har found item ke saath check karna hai
foreach($lostItems as $lostItem){
    foreach($foundItems as $foundItem){

        // Prevent same user matching their own items
        if($lostItem['user_id'] == $foundItem['user_id']){
            continue;
        }

        // Calculate match score
        $score = calculateMatchScore($lostItem, $foundItem);

        // Only keep strong matches
        if($score < 60){
            continue;
        }

        // Check if this match already exists
        $checkMatch = mysqli_query(
            $conn,
            "SELECT * FROM matches
             WHERE lost_item_id='{$lostItem['id']}'
             AND found_item_id='{$foundItem['id']}'"
        );

        if(mysqli_num_rows($checkMatch) > 0){
            continue;
        }

        // Insert the new match
        mysqli_query(
            $conn,
            "INSERT INTO matches
             (lost_item_id, found_item_id, match_score)
             VALUES
             ('{$lostItem['id']}', '{$foundItem['id']}', '$score')"
        );

        // Notify both users about the new potential match
        createNotification($conn, $lostItem['user_id'], "A potential match was found for your lost item.", "match");
        createNotification($conn, $foundItem['user_id'], "Your found item may match a lost report.", "match");
    }
}

?>
