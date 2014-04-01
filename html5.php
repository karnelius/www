<?php
header("Content-type: text/event-stream");
header("Cache-Control: no-cache");
//generate random number for demonstration
$new_data = rand(0, 1000);
//echo the new number
if(isset($_POST['a'])) $a = $_POST['a'];
echo "data: Случайное число: $new_data и а=$a";
flush();

?>