<?php
require_once 'elements/phpself.php';

//Содержание страницы -----------------------------Page------			

$page_header = 'Участники сети';
if ($page_title == '') $page_title = $page_header;
if ($loggedin)
{
		if(isset($_GET['view']))
		{
			$view = sanitizeString($_GET['view']);
			
			if ($view == $_SESSION['user']) 
			{
				$name1 = "Вашa";
				$name2 = "Ваши";
			}
			else $name1 = $name2 = $view;
			
		}
		
		if(isset($_GET['add']))
		{
			$add = sanitizeString($_GET['add']);
			$query = "SELECT * FROM friends WHERE user='$add' AND friend='$user'";
			
			if(!mysql_num_rows(queryMysql($query)))
			{
				$query = "INSERT INTO friends VALUES ('$add', '$user')";
				queryMysql($query);
			}
		}
		elseif (isset($_GET['remove']))
		{
			$remove = sanitizeString($_GET['remove']);
			$query = "DELETE FROM friends WHERE user='$remove' AND friend='$user'";
			queryMysql($query);
		}
		
		$result = queryMysql("SELECT user FROM members ORDER BY user");
		$posts = mysql_num_rows($result);
		if(isset($_POST['num'])) $_SESSION['num'] = sanitizeString($_POST['num']); 
		if($_SESSION['num'] != '') $num = $_SESSION['num']; 
		else $num = 10;
		if($num == 50) $sel50 = "selected='selected'";
		elseif($num == 20) $sel20 = "selected='selected'";
		else $sel10 = "selected='selected'";
		if(!isset($_GET['p'])) $page = 1;
		else $page = sanitizeString($_GET['p']); //изымаем номер страницы
		$total = intval(($posts - 1) /$num) + 1; //Общее число страниц
		$page = intval($page);
		if(empty($page) || $page < 0) $page = 1;
		if($page > $total) $page = $total; //действия при перегрузках
		$start = $page * $num - $num; //С какого места начинать вывод сообщений из БД
		$finish = $start + $num;
		$result = mysql_query("SELECT user FROM members ORDER BY user LIMIT $start, $finish");
		$content = "<form method='post' action=''><div class='top_panel_mess' >   По <select name='num' size='1' onchange='this.form.submit()' >
  <option value='10' $sel10 >10</option>
  <option value='20' $sel20 >20</option>
  <option value='50' $sel50 >50</option>
</select>
</span></div></form><ul>";
		for ($j = 0; $j < $num; ++$j)
		{
			
			$row = mysql_fetch_row($result);
			if ($row[0] == $user) continue;
			//$stat = online($row[0]);
			if(!$row == '')
			{
				$mini = showMini($row[0]);
				$content = $content. "<li class='pattern'>$mini<a href='index.php?view=$row[0]'>$row[0] $stat</a>";
				$query = "SELECT * FROM friends WHERE user='$row[0]' AND friend='$user'";
				$t1 = mysql_num_rows(queryMysql($query));
				
				$query = "SELECT * FROM friends WHERE user='$user' AND friend='$row[0]'";
				$t2 = mysql_num_rows(queryMysql($query));
				$follow = 'добавить';
				
				if(($t1+$t2) >1)
				{
					$content = $content. " &harr; ваш друг";
				}
				elseif($t1)
				{
					$content = $content. " &larr; вы предложили дружбу";
				}
				elseif($t2)
				{
					$fallow = 'добавить';
					$content = $content. " &rarr; предложил вам дружбу";
				}
				if(!$t1)
				{
					$content = $content. " [<a href='members.php?add=".$row[0]."'>$follow</a>]";
				}
				else
				{
					$content = $content. " [<a href='members.php?remove=".$row[0]."'>отменить</a>]";
				}
				$content = $content. "<a href='messages.php?new=$row[0]' class='mess_a' ><img src='images/mail.png' alt='&#9993;' /></a> <a href='dialogues.php?to=$row[0]' class='mess_a' ><img src='images/dial2.png' alt='&#9993;' /></a></li>";
			}
		} 
		$content = $content. "</ul>";
		$naw = pageNavigator($page, $total, "?view=$user&amp;p");
		$content = $content.$naw;
}
else $content = infoStatus( 2, "Для просмотра этой страницы нужно войти на сайт");
$active = "class='active'";
//Подсвечваем активный элемент меню 
$members_active = "class='active'";
require_once 'elements/hmenu.php';// Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
require_once 'elements/htmlself.php';

?>
