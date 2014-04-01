<?php
//Проверям есть ли переменные на добавление

if(isset($_POST['mess']))
{
	
	//Стартуем сессию для записи логина пользователя
	session_start();
	include("functions.php");//Принимаем переменную сообщения
	
	$var = $_POST['mess'];
	 $a = str_replace("\\","", $var);
	$decode = json_decode($a, true);
	$mess = sanitizeString($decode['mess']);
	$sender = $_SESSION['user'];
	$recipient = $decode['recip'];
	$date = date('Y.m.d  H:i:s');
	//echo "connect = true <br /> $mess <br /> $recipient <br /> $sender";
	//Переменная с логином пользователя
	if ($sender != "" && $recipient != "" && $mess != "")
	{
	//Подключаемся к базе
	//echo "<br />прошло";
	$result = mysql_query("INSERT INTO `messages` VALUES (NULL, '$sender', '$recipient', '$mess', '', '$date', 0, 0)");

	
	
	
	

	} 
}
?>