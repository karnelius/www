<?php
	
		
		session_start();
		include("functions.php");
		$login=$_SESSION['user'];
		if ($login != '')
		{
			$last_time = time();
			$query = "UPDATE members SET status='$last_time' WHERE user='$login'";
			mysql_query($query);
		}

?>