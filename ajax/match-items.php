<?php

include("../includes/db.php");
include("../includes/functions.php");

// GET ALL LOST ITEMS

$lostQuery = mysqli_query(

    $conn,

    "SELECT * FROM lost_items"
);

// LOOP LOST ITEMS

while($lostItem = mysqli_fetch_assoc($lostQuery)){

    // GET ALL FOUND ITEMS

    $foundQuery = mysqli_query(

        $conn,

        "SELECT * FROM found_items"
    );

    // LOOP FOUND ITEMS

    while($foundItem = mysqli_fetch_assoc($foundQuery)){

        // PREVENT SAME USER MATCHING OWN ITEMS

        if($lostItem['user_id'] == $foundItem['user_id']){

            continue;
        }

        // CALCULATE MATCH SCORE

        $score = calculateMatchScore(

            $lostItem,

            $foundItem
        );

        // ONLY STRONG MATCHES

        if($score >= 60){

            // CHECK DUPLICATE MATCH

            $checkMatch = mysqli_query(

                $conn,

                "SELECT *

                 FROM matches

                 WHERE lost_item_id='{$lostItem['id']}'
                 AND found_item_id='{$foundItem['id']}'"
            );

            // INSERT ONLY IF NO MATCH EXISTS

            if(mysqli_num_rows($checkMatch) == 0){

                // INSERT MATCH

                mysqli_query(

                    $conn,

                    "INSERT INTO matches

                    (lost_item_id, found_item_id, match_score)

                    VALUES

                    (
                        '{$lostItem['id']}',
                        '{$foundItem['id']}',
                        '$score'
                    )"
                );

                // LOST USER NOTIFICATION

                createNotification(

                    $conn,

                    $lostItem['user_id'],

                    "A potential match was found for your lost item.",

                    "match"
                );

                // FOUND USER NOTIFICATION

                createNotification(

                    $conn,

                    $foundItem['user_id'],

                    "Your found item may match a lost report.",

                    "match"
                );
            }

        }

    }

}

// echo "Matching Completed";

?>