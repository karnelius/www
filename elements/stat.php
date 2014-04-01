<?php 

function online($who)
{
//Кто онлайн? - спросишь ты.
include("functions.php");
$query = "SELECT status FROM members WHERE user='$who'";
$result = mysql_query($query);
$num = mysql_result($result, 0, 'status');
$what = time() - $num;
if ($num <= 120) return "offline";
else return "online";
}

?>