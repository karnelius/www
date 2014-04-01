<?php
//Подключаемся к БД
session_start();
include("functions.php");
$you = $_POST['to'];
$id = $_POST['id'];
$user = $_SESSION['user'];
$result = mysql_query("SELECT * FROM `messages` WHERE `id`>$id AND (`sender`='$you' AND `recipient`='$user' OR `sender`='$user' AND `recipient`='$you') ORDER BY `id`");
$rows = mysql_num_rows($result);
if($rows > 0) {
	$return = array();
	for ($i = 0; $i < $rows; ++$i)
	{
		$row = mysql_fetch_row($result);
		//если я получатель AND если статус сообщени 0, то делаем апргрейд сообщения по ID
		if($row[2] == $user && $row[7] == 0)  mysql_query("UPDATE `messages` SET `read`=1 WHERE `id`=$row[0]");
			
		$return[$i] = array('author' => "$row[1]", 'time' => "$row[5]", 'message' => "$row[3]", 'status' => "$row[7]", 'id' => "$row[0]");

	}
	echo json_encode($return);
}
else 
{
$b = array( 'a' => 'string', 'b' => 'leto', 'c' => 4); 
echo json_encode($b);	
}
?>