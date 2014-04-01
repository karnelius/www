<?php
	session_start();
	$user = $_SESSION['user'];
	include("functions.php");//Принимаем переменную 
	if(isset($_POST['word'])) 
	{
		$str = $_POST['word'];
		$str_p = true;
	}
	if(isset($_POST['link'])) 
	{
		$str = $_POST['link'];
		$str_p = false;
	}
 	$q="SELECT `user` FROM `members` WHERE `user` LIKE '{$str}%' AND `user`!='$user'";
 	$result=mysql_query($q);
 
 if ($result != '') 
 {
  $rows = mysql_num_rows($result);
	for ($i = 0; $i < $rows; ++$i) 
	{
		$row = mysql_fetch_row($result);
		if ($str_p == true) echo '<p onclick="sel((this).innerHTML)">'.$row[0].'</p>';
		else echo "<a href='?to=$row[0]' onclick='sel((this).innerHTML)'>$row[0]</a><br />";
	}
 }

?>