<?php
	require_once 'elements/phpself.php';
//Содержание страницы -----------------------------Page------
if($loggedin)
{
	//-------------------------------Удаление и добавление друзей------начало----------
	if(isset($_GET['add']))
		{
			
			$add = sanitizeString($_GET['add']);
			$add = register($add);
			$query = "SELECT * FROM friends WHERE user='$add' AND friend='$user'";
			
			if(!mysql_num_rows(mysql_query($query)))
			{
				
				$query = "INSERT INTO friends VALUES ('$add', '$user')";
				queryMysql($query);
			}
		}
		elseif (isset($_GET['remove']))
		{
			
			$remove = sanitizeString($_GET['remove']);
			//$remove = register($remove);
			$query = "DELETE FROM friends WHERE user='$remove' AND friend='$user'";
			queryMysql($query);
		}
	//--------------------------------Удаление и добавление друзей------конец-----------
	//------------------зззз
	if(isset($_GET['view'])) 
{
	$view = sanitizeString($_GET['view']);
	$view = register($view);
	/*раздел где мы выводим маленькие картинки возле профиля
	if (!isset($im_name)) $im_name = $user;
	else $im_name = $view;
	$img = indexPhoto($im_name);*/
	
	$page_title = "Страница $view";
}
else $view = $user;

if($view == $user)
{
	$name1 = "Ваш";
	$name2 = "Ваши";
	$name3 = "Вы";
	$update_link = "<div id='redact' style='display: none'><form method='post' action='' enctype='multipart/form-data' >
<p>Введите или отредактируйте сведения о себе / Загрузите изображение</p>
<textarea name='text' cols='30' rows='3'>$text</textarea><br />
Выбрать рисунок: <br /><input type='file' name='image' size='14'  />
<br /><input type='submit' class='submit' value='Сохранить изменения' />
</form><br /></div>
[<a href='javascript: redactVis();'  > редактировать профиль</a>]";

}
else
{
	$name1 = "<a href='members.php?view=$view'>$view</a>'s";
	$name2 = "$view";
	$name3 = "$view";
	$update_link = FALSE;
	$new_mess = "[<a href='messages.php?new=$view' ><img src='images/mail.png' alt='Написать сообщение' /> Написать сообщение пользователю $view</a>]<br />[<a href='dialogues.php?to=$view' ><img src='images/dial2.png' alt='Написать в Диалогах' /> Написать $view в Диалогах </a>]";
}//----------------------------------зззззз
	
	$page_header = "Страница $view";
	if ($page_title == '') $page_title = $page_header;
	$content = '';
	if(isset($_POST['text']) && $_POST['text'] != '')
	{
		$text = sanitizeString($_POST['text']);
		$text = preg_replace('/\s\s+/',' ', $text);
		$query = "SELECT * FROM profiles WHERE user='$user'";
		if(mysql_num_rows(queryMysql($query)))
		{
			queryMysql("UPDATE profiles SET text='$text' WHERE user='$user'");
		}
		else 
		{
			$query = "INSERT INTO profiles VALUES ('$user', '$text')";
			queryMysql($query);
		}
	}
	else
	{
		$query = "SELECT * FROM profiles WHERE user='$user'";
		$result = queryMysql($query);
		if(mysql_num_rows($result))
		{
			$row = mysql_fetch_row($result);
			$text = stripslashes($row[1]);
		}
		else $text = '';
	}

$text = stripslashes(preg_replace('/\s\s+/', ' ',  $text));

if (isset($_FILES['image']['name']))
{
	//все в нижний регистр
	
	$saveto = "images/users/$user_tolower.jpg";
	move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
	$typeok = TRUE;
	
	switch($_FILES['image']['type'])
	{
		case "image/gif" : $src = imagecreatefromgif($saveto); break;
		case "image/jpeg": $src = imagecreatefromjpeg($saveto); break;
		case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
		case "image/png": $src = imagecreatefrompng($saveto); break;
		default : $typeok = FALSE; break;
	}
	
	if($typeok)
	{
		list($w, $h) = getimagesize($saveto);
		$max = 200;
		$tw = $w;
		$th = $h;
		
		if($w > $h && $max < $w) 
		{
			$th = $max / $w * $h;
			$tw = $max;
		}
		elseif($h > $w && $max <$h) 
		{
			$tw = $max / $h * $w;
			$th = $max;
		}
		elseif($max < $w)
		{
			$tw = $th = $max;
		}
		$tmp = imagecreatetruecolor($tw, $th);
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
		imageconvolution($tmp, array(
								array(-1, -1, -1),
								array(-1, 16, -1),
								array(-1, -1, -1)
								), 8, 0);
		imagejpeg($tmp, $saveto, 100);
		imageresize("images/users/mini/$user_tolower.jpg","$saveto",20,100);
		imagedestroy($tmp);
		imagedestroy($src);
	}
}

$show = showProfile($view);

//------------------------------Вывод друзей----------------------
require_once 'elements/friends.php';
//----------------------------Конец вывода друзей
$content = $show.$update_link.$new_mess."<br /><br />";


}//Если позователь не авторизован выполняем код ниже
if (!$loggedin){
if(isset($_GET['view'])) 
{
	$view = sanitizeString($_GET['view']);
	$view = register($view);
	$page_title = $page_header = "Страница $view";
	$content = infoStatus(2, "Для просмотра страницы пользователя <strong>$view</strong> Вам необходимо <a href='registration.php'  >зарегистрироватсья</a> или войти на сайт");
}
else
{
	$page_header = 'Alex chat';
	if ($page_title == '') $page_title = $page_header;
	$content = "<img src='images/sexy_bender.png' alt='Dender' id='bender' /><p>Добро пожаловать на alexchat.net</p> 
<p>Чтобы присоединиться к сообществу пользователей alexchat.net рекомендуем вам зарегистрироваться. </p>
<p><a href='registration.php' class='buttons_big' >Регистрация</a></p>
<p>Чтобы читать статьи пользователей регистрироваться не обязательно. </p>
<p><a href='blog.php?page=1' class='buttons_big'>Статьи</a></p>
<p>Блек-Джек и шлюхи на подходе:)</p>";
}
}
//Конец содержания страницы 

//Подсвечваем активный элемент меню 
$index_active = "class='active'";
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
	
	require_once 'elements/htmlself.php';
?>
