<?php
require_once 'functions.php';
 //функции-----------------------------------------------------------------
 #блок для сбора статистики. требудет дополнительной таблицы в БД
 /*$ref = $_SERVER['HTTP_REFERER']; //referer
 $ip = $_SERVER['REMOTE_ADDR'];// ip-address
 $url = $_SERVER['REQUEST_URI']; // 
 $result = mysql_query("INSERT INTO `stat` VALUES (NULL,'$ref', '$ip', '$url', 4)");*/ 
//хейдер
session_start();
if ((isset($_SESSION['user'])) && (substr($_SESSION['id'], 0, 3) == substr($_SERVER['REMOTE_ADDR'], 0, 3))) //Если в сессии имеется имя пользователя и первые 3 цифры ip при авторизации и сейчас совпадают - пользователь наш. 
{
	$user = $_SESSION['user']; //Изымаем занчение из сессии в переменную $user
	$user_tolower = strtolower($user);
	$loggedin = TRUE;
	
}
else //Иначе убиваем перменную сессии. Пользователь не наш. 
{
	$loggedin = FALSE;
	unset($_SESSION['user']);
}

//Форма входа - начало ------------------------------------------------------
if($_POST['exit']) // Начало кнопки "выход"
{
	if(isset($_SESSION['user']))
	{
		destroySession();
		header("Location: index.php");
	}
}
//Конец кнопки "выход"

	$error = $login = $pass = '';
	if(isset($_POST['login']))
	{
		$login = sanitizeString($_POST['login']);
		$pass = sanitizeString($_POST['pass']);
	
		if ($login == '' || $pass == '')
		{
			$error = infoStatus(3, "Данные введены не во все поля");
		}
		else
		{
			$query = "SELECT user, pass FROM members WHERE user='$login' AND pass='$pass'";
			$result = mysql_query($query);
			
		
			if(mysql_num_rows($result) == 0)
			{
				$error = infoStatus(3, "Неверный логин или пароль");
			}
			else 
			{
				$row = mysql_fetch_row($result);
				$_SESSION['user'] = $row[0];
				$_SESSION['pass'] = $pass;
				$_SESSION['id'] = $_SERVER['REMOTE_ADDR'];
				header("Location: index.php");
			}
		}
	}

if(!$loggedin)
{
	$login_form = <<<_END
				<h3>Вход</h3>
				<form action="" method="post" name='login' >$error
					Логин: <input type="text" name="login" size="25" class='width190' />
					Пароль: <input type="password" name="pass" size="25" class='width190' />
					<input  type="submit" class="submit" value="Войти" />
				</form>
_END;



}
else // если $loggedin не пуста - выводим кнопку выхода. 
{
	$onlineScript = "<script type='text/javascript' >
online();
setInterval(online,100000);
</script>"; // Если вошел  - врубаем ослеживание онлайн
	
	$login_form = "<h3>Выход</h3><p>Чтобы покинуть сайт нажмите кнопку Выход.</p><form action='' method='post' ><input type='submit' name='exit' class='submit' value='Выход' /> </form>";
}
//Форма входа  - конец
$random_article_header = "*Случайная статья*";
$query = "SELECT `id` FROM `articles`";
$rows = mysql_num_rows(mysql_query($query)) - 1;
$random_art = rand(0, $rows);
$result = mysql_query("SELECT `preview`, `id` FROM `articles` LIMIT $random_art, 1");
$art = mysql_fetch_row($result);
if($art[1] != '')  $random_article = $art[0]."<a href='blog.php?id=$art[1]' >[Читать далее]</a>";
else $random_article = infoStatus(2, "Еще нe создано ни одной статьи.");

//Есть ли новые сообщения? 
 $is_new_mess = isMessages ($user);
?>