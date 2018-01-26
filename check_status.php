<?php
//
// Renvoi le status du dernier fichier uploadé
//
//

$user_token = "user_token";
$vid = "videokey";
$video = json_decode(file_get_contents("http://netu.tv/actions/file_uploader.php?checkVideo=1&user_hash=".$user_token."&vid=".$vid),true);
var_dump($video);
?>
