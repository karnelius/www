
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Документ без названия</title>
<script type="text/javascript" >
function start() {
	document.getElementById('myAudio').play();
}
</script>
<link type='text/css' rel='stylesheet' href='styles/main.css' />
<!--[if IE]> <link rel="stylesheet" type="text/css" href="styles/ie_wtf.css" /> <![endif]-->




</head>
<body>
<audio id="myAudio"  >
<source src="media/button1.wav" type="audio/wav"  />
</audio>
<a href="#" onclick="start()">Играй же ж</a>
<?php
require_once 'elements/base.php';
		$sock = 'Polson';
		$to = 'lisa';
		$result = mysql_query("SELECT * FROM `messages` WHERE `sender`='$sock' AND `recipient`='$to' OR `sender`='$to' AND `recipient`='$sock' ORDER BY `id`");
		$rows = mysql_num_rows($result);
		echo $rows;
?>
<a href="https://github.com/progit/progit/blob/master/ru/02-git-basics/01-chapter2.markdown">нужная ссылка</a>

</body>
</html>