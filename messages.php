<?php
	require_once 'elements/phpself.php';
//Содержание страницы -----------------------------Page------messages
/*
Значит такие дела. Тут будет страница с ообщениями. 
будет 3 главных страницы входыщие, исходящие и новое сообщение.

Код, скажи, что мне делать?
Моя жизнь - дерьмо. 
*/

if($loggedin)
{
	
	
	
	
	if(isset($_GET['outbox']) || isset($_GET['inbox'])) //выводит входящие и исходящие
	{
		if(isset($_GET['outbox'])) //разница между входящими и исходящими в трех переменных
		{
			$out_active = "mess_links_active";
			$db_user = 'sender';
			$page_string = '?outbox';
			$n = 2;
			$delete_status = 2; //критический статус. 
			$info = "исходящего";
			$page_title = "Исходящие сообщения";
			if($_GET['outbox'] == '' || !is_numeric($_GET['outbox'])) $page = 1;
			else $page = sanitizeString($_GET['outbox']); //изымаем номер страницы
		}
		else 
		{
			$in_active = "mess_links_active";
			$db_user = 'recipient';
			$page_string = '?inbox';
			$n = 1;
			$delete_status = 1;
			$info = "входящего";
			$page_title = "Входящие сообщения";
			if($_GET['inbox'] == '' || !is_numeric($_GET['inbox'])) $page = 1;
			else $page = sanitizeString($_GET['inbox']); //изымаем номер страницы
		}
		if(isset($_POST['num'])) $_SESSION['num'] = sanitizeString($_POST['num']); 
		if($_SESSION['num'] != '') $num = $_SESSION['num']; 
		else $num = 10;
		if($num == 50) $sel50 = "selected='selected'";
		elseif($num == 20) $sel20 = "selected='selected'";
		else $sel10 = "selected='selected'";
		
		if(isset($_GET['send'])) $add_status = infoStatus(1,"Сообщение успешно отправлено");
		if(isset($_GET['del'])) $add_status = infoStatus(1,"Сообщение успешно удалено");
		
		$query = "SELECT `id` FROM `messages` WHERE $db_user='$user' AND `delete`!=$delete_status";
		$result = mysql_query($query);
		$rows = mysql_num_rows($result);
		
		// переменная $page определена выше
		$total = intval(($rows - 1) /$num) + 1; //Общее число страниц
		$page = intval($page);
		if(empty($page) || $page < 0) $page = 1;/**/
		if($page > $total) $page = $total; //действия при перегрузках
		$start = $page * $num - $num; //С какого места начинать вывод сообщений из БД
		$finish = $start + $num;
		$query = "SELECT * FROM `messages` WHERE $db_user='$user' AND `delete`!=$delete_status  ORDER BY `id` DESC LIMIT $start, $finish";
		$result = mysql_query($query);
		$messages_box = "$add_status<div class='top_panel_mess' ><div class='top_panel_mess_l' >Сортировать по: <strong><a href=''>Дата&#8681;</a> | <a href='#' >Адресат&#8679;</a></strong></div>   По <select name='num' size='1' onchange='this.form.submit()' >
  <option value='10' $sel10 >10</option>
  <option value='20' $sel20 >20</option>
  <option value='50' $sel50 >50</option>
</select>
сообщений</div><p><input type='checkbox' name='all'   /> | <a href='#' >Удалить выделенные</a> | <a href='#' >Пометить как прочитанные</a></p>"; //прикрепляем вернхнюю навигацию к листу сообщений
		if ($rows > 0)
		{
			$row2 = mysql_num_rows($result);
			for ( $i = 0; $i < $row2; ++$i)
			{
				$row = mysql_fetch_row($result);
				$row[3] = strip_tags($row[3]);
				if(mb_strlen($row[3]) > 100) $row[3]  = mb_substr($row[3], 0, 130, 'utf-8');
				
				if ($row[7] == 0) $read_status = "read_status";
				else $read_status = '';
				
				$photo = showMini($row[$n]);
				$messages_box = $messages_box."<div class='message $read_status' >$debug
    <div class='m_select'><input type='checkbox' name='select' value='$row[0]' /></div><!--.m_select -->
    <div class='m_author'>$photo<a href='index.php?view=$row[$n]' ><strong>$row[$n]</strong></a>
            <p>$row[5]</p></div><!--.m_author -->
    <div class='m_mess'><a href='?read=$row[0]' ><strong>$row[4]</strong>
            <p>$row[3]...</p></a></div><!--.m_mess -->
    <div class='m_delete'><a href='?delete=$row[0]' ><img src='/images/recycle_full-16.png' alt='' /></a></div>
 </div><!--.message -->"; //Это паттерн вывода сообщений. 
			}
			$messages_box = $messages_box. pageNavigator($page, $total, $page_string); //прикрепляем нижнюю навигация к листу сообщений	
			$messages_box = "<form action='' method='post' id='outbox_form' >".$messages_box. "</form>";
		}
		else $messages_box = $messages_box. (infoStatus(2, "У Вас нет ни одного $info сообщения")); //если нет сообщений 
		
	}
	
	if(isset($_GET['new']))
	{
		$page_title = "Новое сообщение";
		$new_active = "mess_links_active";
		$to_friend = sanitizeString($_GET['new']);
		$form = true;
	}
	
	if(isset($_GET['delete']) && $_GET['delete'] != '')
	{
		$del_id = sanitizeString($_GET['delete']);
		$query = "SELECT `sender`, `delete` FROM `messages` WHERE `id`=$del_id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($row[0] == $user) //если вы автор, то вы можете встретить два статуса 0 и 1
		{
			if($row[1] == 0) $query = "UPDATE `messages` SET `delete`=2 WHERE `id`=$del_id";
			elseif ($row[1] == 1) $query = "DELETE FROM `messages` WHERE `id`=$del_id"; //если статус удаления равен 1, то удаляем его к чертям
		}
		else 
		{
			if($row[1] == 0) $query = "UPDATE `messages` SET `delete`=1 WHERE `id`=$del_id";
			elseif($row[1] == 2) $query = "DELETE FROM `messages` WHERE `id`=$del_id"; //если статус удаления равен 1, то удаляем его к чертям
		}
		$result = mysql_query($query);
		if($row[0] == $user) header("Location: messages.php?outbox=1&del");
		else header("Location: messages.php?inbox=1&del");
		//тут нужно разобраться со статусами удаления
		
	}
	
	
	if(isset($_GET['read']) && $_GET['read'] != '')
	{
		$id_mess = sanitizeString($_GET['read']);
		$query = "SELECT * FROM `messages` WHERE `id`=$id_mess";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$full_message = $row[3];
		$sender = $row[1];
		$recipient = $row[2];
		$recipient_photo = showMini($row[2]);
		$sender_photo = showMini($row[1]);
		$date = $row[5];
		$theme = $row[4];
		if($sender == $user) $read2 = true;
		else $read = true;
		if($recipient == $user) mysql_query("UPDATE `messages` SET `read`=1 WHERE `id`=$id_mess");
		
	}

	
	if(isset($_POST['message']))
	{
		$sender = $user;
		$recipient = sanitizeString($_POST['friend']);
		$theme = sanitizeString($_POST['theme']);
		if ($theme == "") $theme = "[без темы]";
		$message = sanitizeString($_POST['message']);
		$date = date('Y.m.d  H:i:s');
		if($recipient != '')//если получатель указан - отправляем 
		{
			if($message != '')
			{
				if($sender != $recipient)
				{
					$query ="INSERT INTO messages VALUES (NULL, '$sender', '$recipient', '$message', '$theme', '$date', '0', '0')";
					$result = mysql_query($query);
					$debug = "Эта финформация пойдет в бд: <br /> 
					Автор: $sender<br /> 
					Получатель: $recipient<br /> 
					Тема: $theme<br />
					Текст: $message<br />
					Дата: $date<br />
					";
					$messages_form = $messages_form.$debug;
					header("Location: messages.php?inbox=1&send");
				}
				else $alert = infoStatus(3,"Сам себе пишешь? Упоротый что ли?");
			}
			else $alert = infoStatus(3, "Пустые сообщения не отправляем!");
		}
		else $alert = infoStatus(3, "А ты кому, собственно, пишешь?");
		
		$form = true;	
	}


	
if($form) $messages_form = "<div class='messages_form' >$alert<form action='' method='post' class='create_message' >
		<table>
	<tr>
		<td>
			Кому: 
		</td>
		<td>
			<input type='text' name='friend' id='search_field' onkeyup='search(this.value)' value='$to_friend' /> 
			<div id='search_result'></div>
		</td>
	</tr>
	<tr>
		<td>
			Тема:
		</td>
		<td>
			<input type='text' name='theme' id='theme' size='30' value='$theme' />
		</td>
	</tr>
	<tr>
		<td>
			Сообщение:
		</td>
		<td> 
			<textarea name='message' cols='45' id='message' rows='6'>$message</textarea>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td  style='text-align:center'>
		<input type='submit' class='submit' value='Отправить сообщение' />
		</td>
	</tr>

</table>
</form>

</div><!--.messages_form-->";

if($read) $read_form = "<div class='messages_form' >$alert
<form action='' method='post' class='create_message' >
		<div class='read_mess_ansver_head'>$sender_photo <a href='index.php?view=$sender'><strong>$sender</strong></a><input type='hidden' name='friend'  value='$sender'  /> <p style='float:right'><a href='?delete=$id_mess' ><img src='/images/recycle_full-16.png' alt='Удалить сообщение' /></a><br /><a href='dialogues.php?to=$sender' ><img src='images/dialog-20.png' alt='Перейти в диалоги' /> </a></p> <p>$date</p><br style='float:none' /></div><div class='read_mess_ansver' >$full_message</div>
		<table>
	<tr>
		<td>
			Тема:
		</td>
		<td>
			<input type='text' name='theme' id='theme' size='30' value='Re:$theme' />
		</td>
	</tr>
	<tr>
		<td>
			Сообщение:
		</td>
		<td> 
			<textarea name='message' cols='45' id='message' rows='6'>$message</textarea>
		</td>
	</tr>
	<tr>
		<td colspan='2' style='text-align:center'>
		<input type='submit' class='submit' value='Ответить' />
		</td>
	</tr>

</table>
</form>

</div><!--.messages_form-->";
if ($read2) $read_form = "<div class='read_mess_ansver_head'>$recipient_photo <a href='index.php?view=$recipient'><strong>$recipient</strong></a> <a href='?delete=$id_mess' style='float:right'><img src='/images/recycle_full-16.png' alt='Удалить сообщение' /></a> <p>$date</p><br style='float:none' /></div><div class='read_mess_ansver' >$full_message</div>";

//Есть ли новые сообщения? 
 $is_new_mess = isMessages ($user);
$top_links = "<div class='mess_links'><div class='mess_links_inbox $in_active' ><h3><a href='messages.php?inbox=1' >Входящие $is_new_mess</a></h3></div><div class='mess_links_outbox $out_active' ><h3><a href='messages.php?outbox=1' >Исходящие</a></h3></div><div class='mess_links_new $new_active' ><h3><a href='messages.php?new' >Новое*</a></h3></div><div class='mess_links_outbox ' ><h3><a href='dialogues.php' >Диалоги</a></h3></div></div>";
	$page_header = 'Cообщения';
	if ($page_title == '') $page_title = $page_header;
	$content = $top_links.$messages_box.$messages_form.$read_form;

}
else
{
	$page_header = 'Сообщения';
	if ($page_title == '') $page_title = $page_header;
	$content = infoStatus(2, "Для того, чтобы зайти в раздел <strong>Сообщения</strong>, вам нужно зарегистрироваться или войти на сайт.");
}
//Конец содержания страницы 

//Подсвечваем активный элемент меню 
$messages_active = "class='active'";
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
	
	require_once 'elements/htmlself.php';
?>
