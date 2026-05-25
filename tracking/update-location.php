<?php

session_start();

include("../includes/db.php");

if(!isset($_SESSION['user_id'])){
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$match_id = (int)$_POST['match_id'];

$latitude = mysqli_real_escape_string($conn, $_POST['latitude']);

$longitude = mysqli_real_escape_string($conn, $_POST['longitude']);

$access = mysqli_query(
    $conn,
    "SELECT matches.id
     FROM matches
     JOIN lost_items ON matches.lost_item_id = lost_items.id
     JOIN found_items ON matches.found_item_id = found_items.id
     WHERE matches.id='$match_id'
     AND matches.status='approved'
     AND (lost_items.user_id='$user_id' OR found_items.user_id='$user_id')"
);

if(mysqli_num_rows($access) === 0){
    exit();
}

// CHECK EXISTING LOCATION

$check = mysqli_query(

    $conn,

    "SELECT * FROM live_locations
     WHERE match_id='$match_id'
     AND user_id='$user_id'"
);

if(mysqli_num_rows($check) > 0){

    mysqli_query(

        $conn,

        "UPDATE live_locations

        SET latitude='$latitude',
            longitude='$longitude'

        WHERE match_id='$match_id'
        AND user_id='$user_id'"
    );

}else{

    mysqli_query(

        $conn,

        "INSERT INTO live_locations
        (match_id, user_id, latitude, longitude)

        VALUES
        ('$match_id', '$user_id', '$latitude', '$longitude')"
    );

}
?>
