<?php

session_start();

include("../includes/config.php");
include("../includes/db.php");

$match_id = (int)$_GET['match_id'];

$current_user = (int)$_SESSION['user_id'];

$query = "

SELECT * 

FROM live_locations

WHERE match_id='$match_id'
AND user_id != '$current_user'

LIMIT 1

";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){

    echo json_encode(mysqli_fetch_assoc($result));

}else{

    echo json_encode([]);
}
?>
