<?php
////////////////////////////////////////////////////////
// API version 1.0
//
// for php 5.6+ you need to make some changes in code
// method 1
// add the following line
// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 0);
//
// method 2
// change
// $post_fields['Filedata'] = "@".$file;
// to
// $post_fields['Filedata'] = CURLFile($file);
////////////////////////////////////////////////////////


//REQUIRED Registered Users - You can find your user token in profile page.

$user_token = "$user_token";

function removeBOM($str="") {
    if(substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
        $str = substr($str, 3);
    }
    return $str;
}

function curl($url="",$post_fields=array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result=curl_exec ($ch);

    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    return(array("result" => json_decode(removeBOM($result), true), "code" => $code));
}

//$file = "/var/www/video.mp4";

$file = $argv[1];

if(!file_exists("$file"))
die("ERROR: Can't find '$file'!\n");

$path_parts = pathinfo($file);
$ext = $path_parts['extension'];
$title = $path_parts['basename'];
$allowed = array("flv", "avi", "rmvb", "mkv", "mp4", "wmv", "mpeg", "mpg", "mov","divx","3gp","xvid","asf","rm","dat","m4v","f4v","webm","ogv");

if (!in_array(strtolower($ext),$allowed))
    die("ERROR: Video format not permitted. Formats allowed: wmv,avi,divx,3gp,mov,mpeg,mpg,xvid,flv,asf,rm,dat,mp4,mkv,m4v,f4v,webm,ogv!\n");

$con = file_get_contents("http://netu.tv/plugins/cb_multiserver/api/get_upload_server.php?user_hash=".$user_token);

$converter = json_decode(removeBOM($con), true);

if(isset($converterp['error']))
    die("ERROR: Could not choose converter. Aborting. Error:(".$converter['error'].") \n");

$post_fields['Filedata'] = "@".$file;
$post_fields['upload'] = "1";

foreach ($converter as $key => &$value) {
    $post_fields[$key] = $value;
}

$result = curl($converter['upload_server'],$post_fields);

if($result['code'] == 200){

    if(isset($result['result']['success']))
    {
        $post_fields = array();
        $post_fields['insertVideo'] = "yes";
        $post_fields['title'] = $title;
        $post_fields['server='] = $converter['upload_server'];
        $post_fields['user_hash'] = $user_token;
        foreach ($result['result'] as $key => &$value) {
            $post_fields[$key] = $value;
        }
        $result_insert = curl("http://netu.tv/actions/file_uploader.php",$post_fields);
        if($result_insert['code'] == 200){
            var_dump($result_insert['result']);
        }else {
             echo $result_insert['result'];
        }
    }else{
        echo $result['result'];
    }
}else{
        echo $result['result'];
}
?>
