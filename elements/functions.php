<?php
$dbhost = 'localhost';
 $dbname = 'alexdb'; // Необходимо создань новую базу данных, нового пользователя и пароль. 
 $dbuser = 'alex';
 $dbpass = 'alexpass';
 $appname = 'Чат для Alex';
 
/*$dbhost = 'db02.hostline.ru';
 $dbname = 'vh157777_alexdb'; // Необходимо создань новую базу данных, нового пользователя и пароль. 
 $dbuser = 'vh157777_alex';
 $dbpass = '2363669al';
 $appname = 'Чат для Alex';*/
 
 
 mysql_connect($dbhost, $dbuser, $dbpass) or die( "Ошибка в позиции 1".mysql_error()); // Подключаемся к базе данных
 mysql_select_db($dbname) or die(" Ошибка в позиции 2".mysql_error()); // Выбираем саму базу данных
 
 function register($name)
 {
	//Борьба с нижним регистром. 
	$result = mysql_query("SELECT `user` FROM `members` WHERE `user` LIKE '$name'");
	$row = @mysql_fetch_row($result);
	return $row[0];
 }
 
 function userNetwork($user)
 {
	$followers = array(); //делаем два массива
	$following = array();
	$query = "SELECT * FROM friends WHERE user='$user'";
	$result = queryMysql($query);
	$num = mysql_num_rows($result);
	
	for ($j = 0; $j < $num; ++$j) //забиваем в массив результаты
	{
		$row = mysql_fetch_row($result);
		$followers[$j] = $row[1];
	}
	
	$query = "SELECT * FROM friends WHERE friend='$user'";
	$result = queryMysql($query);
	$num = mysql_num_rows($result);
	
	for ($j = 0; $j < $num; ++$j) // и тут забиваем результаты
	{
		$row = mysql_fetch_row($result);
		$following[$j] = $row[0];
	}
	
	$mutual = array_intersect($followers, $following); // делаем массив, в котором совпадают юзеры. это "друзья"
	$followers = array_diff($followers, $mutual); // исклюаем из массива "подписчиков" "друзей"
	$following = array_diff($following, $mutual); // исключаем из массива "заявок" "друзей"
	
	$sum = array('friends' => $mutual, 'followers' => $followers, 'following' => $following); 
	return $sum;
 }


 
 function comments ($by_id)
 {
	 $query = "SELECT * FROM comments WHERE artid='$by_id' ORDER BY id DESC";
	$result = mysql_query($query);
	return $result;
	 
 }
 
 function timeStatus ($who)
 {
	$query = "SELECT status FROM members WHERE user='$who'";
	$result = mysql_query($query);
	$num = @mysql_result($result, 0, 'status'); //извлекаем ячейку с подавлением ошибки. 
	return $num;
 }
 
 function online($who)
{
	//Кто онлайн? - спросишь ты.
	$num = timeStatus ($who);
	$what = time() - $num;
	if ($what > 300) return "offline";
	else return "<span class='online_text' >online</span>";
}

 function imageresize($outfile,$infile,$percents,$quality) //создание мелокого изображения
 {
    $im=imagecreatefromjpeg($infile);
    $w=imagesx($im)*$percents/100;
    $h=imagesy($im)*$percents/100;
    $im1=imagecreatetruecolor($w,$h);
    imagecopyresampled($im1,$im,0,0,0,0,$w,$h,imagesx($im),imagesy($im));

    imagejpeg($im1,$outfile,$quality);
    imagedestroy($im);
    imagedestroy($im1);
    }
 
 function showMini ($name)
 {
	 $name = strtolower($name);
	  	if( file_exists("images/users/mini/$name.jpg"))
 			return "<div class='mini_photo' ><a href='index.php?view=$name'  ><img src='images/users/mini/$name.jpg' alt='$name' /></a></div>";
 		else return "<div class='mini_photo' ><a href='index.php?view=$name'  ><img src='images/users/mini/default.jpg' alt='Нет фото' /></a></div>";
 }
 
 function isMessages ($user) {
	$query = "SELECT `id` FROM `messages` WHERE `recipient`='$user' AND `read`=0";
	$result = mysql_query($query);
	$rows = mysql_num_rows($result);
	if($rows > 0) return "($rows)";
	else return ""; 
 }
 
 function createTable($name, $query)
 {
	 if (tableExists($name))
	 {
		 echo "Таблица '$name' уже существует <br />";
	 }
	 else 
	 {
		 queryMysql("CREATE TABLE $name($query)");
		 echo "Таблица '$name' создана <br />";
	 }
 }
 
 function tableExists($name)
 {
	 $result = queryMysql("SHOW TABLES LIKE '$name'");
	 return mysql_num_rows($result);
 }
 
 function queryMysql($query)
 {
	 $result = mysql_query($query);
	 return $result;
 }
 
 function destroySession() //функция урезана. сейчас она не работает с куками. 
 {
	//$_SESSION = array(); 
	//if (session_id() != '' || isset($_COOKIE[session_name()]))
	 //	setcookie(session_name(), '', time()-2592000, '/');
	session_destroy();
	$loggedin = FALSE;
 }
 
 function sanitizeString($string)
 {
	$string = strip_tags($string);
	$string = htmlentities($string, ENT_QUOTES, "utf-8");
	$string = stripslashes($string);
	return mysql_real_escape_string($string);
 }
 
 function indexPhoto($name)
 {
	$result = mysql_query("SELECT `gender` FROM `members` WHERE `user`='$name'");
	$gender = mysql_fetch_row($result);
	if ($gender[0] == 'm') $img = "<img src='images/male.png' alt='m' />"; 
	elseif ($gender[0] == 'w') $img = "<img src='images/female.png' alt='w' />";
	elseif ($gender[0] == 'n') $img = "<img src='images/person.png' alt='n' />"; 
	return $img;
 }
 
 function showProfile($user)
 {
	 $time =  timeStatus($user);
	 if($time == '') $time = "неизвестно когда";
	 else
	 {
		 if((time() - $time ) < 300) $time = "пользователь <span class='online_text' >online</span>";
		 else {
		 $time = $time - 3600;
		 $time = date('Y.m.d  H:i:s', $time);
		 }
	 }
	 $a = "<br />Был на сайте: $time <br />[<a href='blog.php?author=$user' title='Блог $user'><img src='images/image-a-54.png' alt='blog' />Показать блог $user</a>]<br />";
	 $img = strtolower($user);
	 if( file_exists("images/users/$img.jpg"))
	 	$string = "<img src='images/users/$img.jpg' border='1' alt='$user photo' />";
		else $string = "<img src='images/users/unknown-person.png' border='1'  alt='$user photo' />";
	 $result = mysql_query("SELECT * FROM profiles WHERE user='$user'");
	 if(mysql_num_rows($result))
	 {
		 $row = mysql_fetch_row($result);
		 $string = $string."<blockquote>$user: ". stripslashes($row[1])."</blockquote>";
		 return $string.$a;
	 }
	 else return $string.$a;
 }
 //валидация 
 function validate_username($field)
{
	if($field == "") return "&#65515; Не указано имя пользователя. <br />";
	else if (strlen($field) < 3) return "&#65515; Имя пользовател не может быть короче 3 символов. <br />";
	else if (strlen($field) > 15) return "&#65515; Воу-воу! Полегче! Не более 16 букв в логине. <br />";
	else if (preg_match("/[^\w]/", $field)) return "&#65515; Имя пользотеля должно состоять только из латинских букв, цифр и символа \"_\". <br />";
	else return "";
}
function validate_password($field)
{
	if($field == "") return "&#65515; Не указан пароль. <br />";
	else if (strlen($field) < 4) return "&#65515; Пароль не может быть короче 4-и символов. <br />";
	else if (preg_match("/[^\w]/", $field)) return "&#65515; Пароль должен состоять только из латинских букв, цифр и символа \"_\". <br />";
	return "";
}

function validate_email($field)
{
	if($field == "") return "&#65515; Не указан email. <br />";
	else if (!preg_match("/[\w\.]+@\w+\.\w+/", $field)) return "&#65515; Email не соответстует формату <br />";
	return "";
}

function infoStatus($stat, $string) 
{
	if ($stat != '' && $string != '')
	{
		if($stat == 1) $img = "apply";
		elseif($stat == 2) $img = "info";
		elseif($stat == 3) $img = "error";
		else  $img = "smile";
		return "<div class='send_status_comp' ><img src='images/$img.png' alt='$img' /> $string</div>";
	}
	return "<div class='send_status_comp' ><img src='images/smile.png' alt='smile' />Ошибка сообщения об ошибке</div>";
}

function validate_captcha($field)
{
	if($field == "") return "&#65515; Защитный код не введен <br />";
	else if(strtolower($field) != $_SESSION['captcha'])
	{
		
		 return "&#65515; Защитный код введен неправильно <br />";
	}
	return "";
}
function validate_gender($field)
{
	if($field == "") return "&#65515; Пол не указан <br />";
	else return "";
}

function pageNavigator($page, $total, $link)
{
	
	//******************
		if ($page != 1) $pervpage = "<a href='$link=1' title='В начало'><img src='images/doubleleft.png' alt='В начало' /></a>
		<a href='$link=".($page-1)."' title='Назад' ><img src='images/onesize-008.png' alt='Назад' /></a> ";
		if ($page != $total) $nextpage = "<a href='$link=".($page + 1)."' title='Далее'><img src='images/left.png' alt='Далее' /></a>
		<a href='$link=$total' title='В конец' ><img src='images/onesize-008double.png' alt='В конец' /></a>";
	//находим ближайшие страницы
		if ($page - 2 > 0) $page2left = " <a href='$link=".($page - 2)."'>".($page - 2)."</a> | "; 
		if ($page - 1 > 0) $page1left = " <a href='$link=".($page - 1)."'>".($page - 1)."</a> | ";
		if ($page + 2 <= $total) $page2right = " | <a href='$link=".($page + 2)."'>".($page +2)."</a>";
		if ($page + 1 <= $total) $page1right = " | <a href='$link=".($page + 1)."'>".($page +1)."</a>";
		return "<div class='page_bottom'  >$pervpage $page2left $page1left<strong>$page</strong>$page1right $page2right $nextpage</div><!--page_bottom-->";
		
}

?>