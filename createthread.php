<style>
body{
color:#f00;
background:#000;
}
</style>
<body>
<?php
include "config.php";
include "board.php";

$postAllowed = true;
$postName = $_POST["name"];
$postText = $_POST["post"];
$postReply = $_POST["reply"];
$postBoard = $_POST["board"];
$postAdmin = NULL; // admin post?

if(isset($postAdmin)){
	if($postAdmin != $bbspass){
		$postAllowed = false;
		echo "ERROR: INCORRECT ADMIN PASSWORD.";
	}
}

if(!isset($postName) or $postName == ""){
	if($postReply == 0){
		$postAllowed = false;
		echo "ERROR: Please enter a title for your thread.";
	} else {
		$postName = indianName();
	}
}
if(!(isset($postText)) or $postText == ""){
	$postAllowed = false;
	echo "ERROR: You didn't enter a post.";
}
if(!(isset($postReply)) or $postReply == ""){
	$postAllowed = false;
	echo "You know what you did, idiot.";
}
if(!(isset($postBoard)) or $postBoard == ""){
	$postAllowed = false;
	echo "Board not specified";
}
if(strlen($postText) > 4096){
	$postAllowed = false;
	echo "Your post is too long. Maximum post size is 4,096 chars.";
}
if(strlen($postName) > 256){
	$postAllowed = false;
	echo "Your post name is too long. Maximum name size is 256 chars.";
}

// Finally, lets get to the posting.
if($postAllowed == true){
	$bbspass = "changeme"; 
	$db = new db($config);
	$db->connect();
	$newPost = array(
	"id" => $db->getPosts()->num_rows+1,
	"name" => htmlspecialchars($postName),
	"date" => date("Y/m/d g:i:s"),
	"reply" => intval($postReply),
	"board" => $postBoard,
	"post" => htmlspecialchars($postText),
	"info" => crypt($_SERVER['REMOTE_ADDR'], $bbspass),  // used for IP banning people without storing their ip as plaintext
	"style" => "normal",
	"fortune" => "none",
	"roll" => "none",
	"admin" => "none"
	);
	$new = $newPost;
	echo $db->addPost($new);
	header("Location: index.php");
} else {
	echo "not allowed";
}
?>
</body>
