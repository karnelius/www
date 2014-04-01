<?php
require_once 'functions.php';
if (isset($_POST['user']))
{
	$user = sanitizeString($_POST['user']);
	$query = "SELECT * FROM members WHERE user='$user'";
	if(mysql_num_rows(queryMysql($query)))
	{
		echo " &#9746;Такой логин уже существует";
	}
	else 
	{
		echo " &#9745; Логин свободен";
	}
}
?>