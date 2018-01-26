<?php
//
// Fait une copie d'un fichier présent sur le compte
// Méthode avec file_get_content()
//

$user_token = "Your_user_token";
$vid = "c31OyK0MeKth"; //Code vidéo
$video = json_decode(file_get_contents("http://netu.tv/actions/file_uploader.php?vidCopy=1&delOrig=0&user_hash=".$user_token."&vid=".$vid),true);
var_dump($video);
?>
