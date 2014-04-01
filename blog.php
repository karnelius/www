<?php
/*
Итак, поехали. 
	если ты авторизован, то доступны: 
	{
		$_GET['new'] - ВЫДАЧА формы на создание/редактирование статей 
		$_POST['comment_new'] -  ПРИЕМ комментария
		$_GET['delcomm'] - ПРИЕМ заявки на удаление комментария
		$_POST['editor'] - ПРИЕМ добавленной статьи
		форма комментариев TRUE
	}
	иначе 
	{
		форма комменатриев FALSE
	}
далее
	$_GET['id'] ВЫДАЧА статьи по ID
	$_GET['author'] - ВЫДАЧА статей "автора" имеет постраничную выгрузку по &p=n
	$_GET['page'] - ВЫДАЧА всех статей по страницам.
	
*/
	require_once 'elements/phpself.php';
if($loggedin)
{
	$blog_comments_form = "<form action='' method='post' id='form_comm' >
			Оставьте свой комментарий<br /><textarea name='comment_new' cols='45' rows='4'></textarea> <br /><br />
			<input type='submit' name='input' value='Оставить комментарий' class='submit' /><br /><br />
			</form>  ";
	
	if( isset($_GET['new']))
	{
		$page_title = "Новая статья";
		if(($_GET['new'] != '') && ($_GET['new'] != 'true'))//если статья не новая, а только редактируется, мы получаем ее id
		{
			$page_title = "Редоктирование статьи";
			$red_id = sanitizeString($_GET['new']);
			$query = "SELECT * FROM articles WHERE id='$red_id'";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
		}
		$create_post = "<div id='sample'>
		<table><form action='blog.php?newid=$red_id' method='post' id='creat_form' >
		<tr>
			<td>Назнвание:</td>
			<td><input type='text' size='50' name='title' value='$row[3]' /></td>
		</tr>
		<tr>
		<td>Тема:</td>
		<td> <input type='text' size='50' name='theme' value='$row[2]' /></td>
		</tr>
		</table>
		<p>Статья:</p> <textarea name='editor' id='editor' rows='8' style='width: 100%;' >$row[6]</textarea>
		<p style='text-align:center;' ><input type='submit'  class='submit' value='Опубликовать' /></p>
		<script type='text/javascript'>
		CKEDITOR.replace( 'editor');
		</script>
		</form></div>";
		$content = $create_post;
		$script = "<script type='text/javascript' src='ckeditor/ckeditor.js'></script>";
	}
	//Обслуживающие GET and POST
	if (isset($_POST['comment_new']) && ($_GET['id'] != '') && ($_POST['comment_new'] != ''))
	{
		$comment_new = sanitizeString($_POST['comment_new']);
		$art_id_new = sanitizeString($_GET['id']);
		$author_new = $user;
		$date_new = date('Y.m.d  H:i:s');
		$query= "INSERT INTO comments VALUES (NULL, '$art_id_new', '$author_new', '$date_new', '$comment_new')";
		$result = mysql_query($query);
		header("Location: blog.php?id=$art_id_new&newcomm");
	}
	if (isset($_GET['delcomm']))
	{
		$dellcomm = sanitizeString($_GET['delcomm']);
		$art_id = sanitizeString($_GET['id']);
		$query = "DELETE FROM `comments` WHERE `id`='$dellcomm'";
		$result = mysql_query($query);
		header("Location: blog.php?id=$art_id&delcomment");
	}
	//----------удаление статьи
	if (isset($_GET['delart']))
	{
		$del_art_id = sanitizeString($_GET['delart']);
		$query = "DELETE FROM articles WHERE id=$del_art_id";
		$result = mysql_query($query);
		header("Location: ?page=1&deleted");
		
		
	
	}
	//----------------конец удаления статьи ****подтверждение language='javascript' onclick="return confirm('Вы действительно хотите удалить эту татью?') запомнить
	
		if (isset($_POST['editor']))// изымаем данные из формы написания статьи. мне придут 3 переменные, остальнвые 
	{
		
		$title = sanitizeString($_POST['title']);
		$theme = sanitizeString($_POST['theme']);
		$text = stripslashes($_POST['editor']);
		$author = $user;
		$date = date('Y.m.d  H:i:s');
		$text = preg_replace( '/<script.*?script>/si', 'скрипт', $text); // заменяем потенциальные скрипты на всякую фигню. 
		$preview = strip_tags($text);
		$preview = mb_substr($preview, 0, 200, 'utf-8').'...';
		if(($_GET['newid'] !="") && ($_GET['newid'] !="true"))
			{
				$id = $_GET["newid"];
				$query= "UPDATE articles SET `author`='$author', `theme`='$theme', `title`='$title', `time`='$date', `preview`='$preview', `text`='$text' WHERE `id`=$id";
				$update = TRUE;
			}
		else {
				$query= "INSERT INTO articles VALUES (NULL, '$author', '$theme', '$title', '$date', '$preview', '$text')";
			}
			
		if ($text != '')
		{
			$result = mysql_query($query);
			if (!$update)
			{
			$subid= mysql_query("SELECT LAST_INSERT_ID()");//изымаем последний назначенный id и направляем на эту станицу пользователя.
			$subid = mysql_fetch_row($subid);
			$id = $subid[0];	
			}
			
			header("Location: blog.php?id=$id&pub");
		}
	}

}
else
{
	$blog_comments_form = infoStatus(2, "Чтобы оставить свой комментарий, тебе необходимо зарегестрироваться или войти на сайт.")."<br /><br />";
}
//=============================================================================всем===========
	if (isset($_GET['id']))//++++++++++++++++++++++++++++++++++ID +++++GET
	{
		
		if (isset($_GET['pub'])) $info_status = infoStatus(1, "Статья успешно <strong>опубликована</strong>");
		if(isset($_GET['delcomment'])) $info_status_comm = infoStatus(1, "Ваш комментарий успешно <strong>удален</strong>");
		if(isset($_GET['newcomm'])) $info_status_comm = infoStatus(1, "Ваш комментарий успешно <strong>добавлен</strong>");
		$id = sanitizeString($_GET['id']);
		$art_id = $id;
		$query = "SELECT * FROM articles WHERE id='$id'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$confirm = '"Вы действительно хотите удалить эту статью? "';
		if (!$row == '')//Если по выбранному id есть что-нибудь в базе данных, то выдаем статью, подгружаем комментарии.
		{
			$author = $row[1];
			$theme = $row[2];
			$title = $row[3];
			$date = $row[4];
			$text = $row[6];
			$page_title = $row[3];
			//если автор и пользователь один и тот же человек, у них появится кнопочка "редактировать". если нет, то не появится.
			if ($author == $user) $redact = "|<a href='?new=$row[0]' title='Изменить'><img src='images/postedit.png' alt='Изменить' />Изменить</a> | <a href='?delart=$row[0]' language='javascript' onclick='return confirm($confirm)' title='Удалить статью'><img src='images/delete.png' alt='Удалить статью' />Удалить</a>";
			else $redact = '';
			//comments
			$result = comments($art_id);
			$comm_rows = mysql_num_rows($result);
			$blog_comment_self = '';
			for ($i = 0; $i < $comm_rows; ++$i)
			{
				$row = mysql_fetch_row($result);
				if ($row[2] == $user) $del_comm = " <a href='?id=$id&amp;delcomm=$row[0]' title='Удалить комментарий'>[X]</a>";
				else $del_comm = '';
				$blog_comment_self = $blog_comment_self."<div class='comm_text' >
				<div class='comm_time' >
				 $row[3] $del_comm</div>
				<h5><a href='index.php?view=$row[2]' >$row[2]</a></h5>
				<p>$row[4]</p>
				</div>";
			}
			//-----------------собираем тело статьи блога------------
			//Переменные блога
			$blog_header= $title ? $title:"[Без названия]";
			$theme = $theme ? $theme:"Без темы";
			$blog_post= "
				$info_status<h3 class='h3blog' >$blog_header</h3>
				<div class='blog_nav' ><img src='images/postdate.png' alt='Дата публикации' />$date |<img src='images/postauth.png' alt='Автор' />Автор: <a href='?author=$author' title='Author'>$author</a>$redact 
				</div>
				<div class='blog_text' >
				$text
				</div>
				<div class='blog_bottom'><img src='images/postcate.png' alt='Тема' />Тема: <i>$theme</i> |<a href='#comments'><img src='images/postcomm.png' alt='Комментарии' />Комментарии ($comm_rows)</a>
				</div><!-- /'blog_bottom' -->";
			//-----------------конец сборки тела стати блога-------
			//-----------------начало комментариев с формой-------
			
			$blog_comment_div = "<a name='comments' id='comments'></a><div class='comment_list' >
			<h4>Комментарии:</h4>$info_status_comm
			$blog_comments_form 
			$blog_comment_self 
			</div><!-- /'comment_list' -->";
			//-----------------конец комменатриев с формой--------
		}
		else //Если под выбранным id ничего не существует в базе данных, выдаем сообщение об ошибке. 
		{
			$blog_post = infoStatus(3,"Ошибка! Статьи не существует.<br /> Возможно она была удалена или перемещена.");;
		}
	}
	
	
	if( isset($_GET['author']) || isset($_GET['page'])) {
		
		if(isset($_GET['deleted'])) $info_status = infoStatus(1, "Статья успешно удалена");
		
		if(isset($_GET['page'])) {
		$page = sanitizeString($_GET['page']); //изымаем количество страниц
		$header_art = "Все статьи";
		$link = "?page";
		$info = "Еще никто ничего не написал";
		$page_title = "Все статьи";
	}
	else if(isset($_GET['author'])){
		
		$author = sanitizeString($_GET['author']); //изымаем автора
		$where = "WHERE `author`='$author'";
		$header_art = "Статьи <a href='index.php?view=$author'>$author</a>";
		$link = "?author=$author&amp;p";
		$info = "У пользователя <a href='index.php?view=$author'>$author</a> еще нет записей в блоге";
		if(!isset($_GET['p'])) $page = 1;
		else $page = sanitizeString($_GET['p']); //изымаем номер страницы автора
	
	}
		if(isset($_POST['num'])) $_SESSION['num'] = sanitizeString($_POST['num']); 
		if($_SESSION['num'] != '') $num = $_SESSION['num']; 
		else $num = 10;
		if($num == 50) $sel50 = "selected='selected'";
		elseif($num == 20) $sel20 = "selected='selected'";
		else $sel10 = "selected='selected'";
		 
		$query = "SELECT `id` FROM articles $where"; //запрос, чито чтобы посчитать
		$result = mysql_query($query);
		$art_rows = mysql_num_rows($result);//узнаем скаолько там вообще статей$posts
		$total = intval(($art_rows - 1) /$num) + 1; //Общее число страниц
		$page = intval($page);
		if(empty($page) || $page < 0) $page = 1;
		if($page > $total) $page = $total; //действия при перегрузках
		$start = $page * $num - $num; //С какого места начинать вывод сообщений из БД
		$finish = $start + $num;
		$query = "SELECT * FROM articles $where ORDER BY id DESC LIMIT $start, $finish";
		$result = mysql_query($query);
		if ($art_rows > 0)
		{
			$blog_post = "$info_status<h3>$header_art</h3><form method='post' action=''><div class='top_panel_mess' >   По <select name='num' size='1' onchange='this.form.submit()' >
  <option value='10' $sel10 >10</option>
  <option value='20' $sel20 >20</option>
  <option value='50' $sel50 >50</option>
</select>
сообщений</span></div></form>";
			for ($i = 0; $i < $num; ++$i)
			{
				$row = mysql_fetch_row($result);
				 $n = ($i + $num * $page)-($num - 1);
				
				if (!$row == "")
				{
					$author = $row[1];
					$theme = $row[2];
					$title = $row[3];
					$date = $row[4];
					$preview = $row[5];
					$comm_rows = mysql_num_rows(comments($row[0]));
					
					if ($author == $user) $redact = "|<a href='?new=$row[0]' title='Изменить'><img src='images/postedit.png' alt='Изменить' />Изменить</a>";
					else $redact = '';
					
					$blog_post = $blog_post."
					<h4 class='preview_art' > <a href='?id=$row[0]' title='Читать полностью'>$title</a></h4>
					<div class='blog_nav' ><img src='images/postdate.png' alt='Дата публикации' />$date |<img src='images/postauth.png' alt='Автор' />Автор: <a href='?author=$author' title='Author'>$author</a>  $redact
					</div>
					<div class='blog_preview'>
					$preview<a href='?id=$row[0]' title='Читать полностью'>[Читать полностью]</a>
					</div>
					<div class='blog_bottom'><img src='images/postcate.png' alt='Тема' />Тема: <i>$theme</i> |<a href='blog.php?id=$row[0]#comments'><img src='images/postcomm.png' alt='Комментарии' />Комментарии ($comm_rows)</a>
					</div><!-- /'blog_bottom' -->";	
				}
			}
				//находим ближайшие страницы
		$naw = pageNavigator($page, $total, $link);
		$blog_post = $blog_post.$naw;
		}
		else //Если запрашиваемый автор еще ничего не написал, так и говорим.
		{
			$blog_post = infoStatus(2, $info);
		}
	}
	/*else //не указан ни id ни author
	{
		$blog_post = "Вариант, когда вы просто зашли на эту сраницу, без дополнительных GET";
	}
	*/

	//-----------------------------GET конец приема



	//Начало сборки авторизованного блога
	$page_header ="Блог ". $author;
	if ($page_title == '') $page_title = $page_header;
	
	$content = $blog_post.$blog_comment_div.$create_post;
	//Конец сборки авторизованного блога




//Конец содержания страницы 

//Подсвечваем активный элемент меню 
$blog_active = "class='active'";
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
	
	require_once 'elements/htmlself.php';
?>
