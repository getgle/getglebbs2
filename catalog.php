<?php
include "board.php";
include "config.php";
$db = new db($config);
$db->connect();
$html = new buildHTML($db);
echo $html->catalog();

?>
