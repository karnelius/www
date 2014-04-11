<?php
	require_once 'elements/phpself.php';
//Содержание страницы -----------------------------Page------
if($loggedin)
{
	$my_friends = 'нету:(';
$sum = userNetwork($user);
$top_links = "<div class='mess_links'><div class='mess_links_inbox $in_active' ><h3><a href='messages.php?inbox=1' >Входящие $is_new_mess</a></h3></div><div class='mess_links_outbox $out_active' ><h3><a href='messages.php?outbox=1' >Исходящие</a></h3></div><div class='mess_links_new $new_active' ><h3><a href='messages.php?new' >Новое*</a></h3></div><div class='mess_links_dial mess_links_active' ><h3><a href='dialogues.php' >Диалоги</a></h3></div></div>
<audio id='myAudio'  >
<source src='media/button1.mp3' type='audio/mpeg'  />
<source src='media/button1.wav' type='audio/wav'  /> 
</audio>"; //Это верхнее меню

if(sizeof($sum['friends']))
{
	foreach($sum['friends'] as $friend)
	{
		$stat = online($friend);
		if ($stat == 'offline') $my_friendsof = $my_friendsof. "<p class='dial_friend_p' ><a href='?to=$friend'>$friend </a>$stat</p>";
		else $my_friendson = $my_friendson. "<p dial_friend_p ><a href='?to=$friend'>$friend </a>$stat</p>";
	}
}
	//-----------------------------------Начало-Активных-Диалогов----------------------
	$page_header = $page_title =  'Диалоги';
if(isset($_GET['to']) && ($_SESSION['user'] != $_GET['to']))
{
	$get_to = sanitizeString($_GET['to']);
	
	
$funcLoad = "<script type='text/javascript'>
	load_messes();
	setInterval('load_messes()',3000);
</script>";

$area = "<div class='left_dial'>
	<div id='messages'>
		<img src='images/loading.gif' alt='load' />
	</div>
	<form action='javascript:send();'>
	<input type='text' id='mess_to_send' onfocus='clearNews()' />
	<input name='Кнопка' type='button' value='Отправить' class='submit'  onclick='send()'  />
	</form>
</div>";

}
else 
{
$styleAuto = "style='width: 80%; float: none; margin: 0 auto;'";
$funcLoad = "";

}


$script = "<script type='text/javascript' src='js/dialogues.js' ></script>";

if ($get_to != '') {
	$photo_friend = showMini($get_to);
	$status = online($get_to);
}
$your_photo = showMini($user);




$content = $top_links. "
<div class='dial_photos' >
	<div class='mailto'>$photo_friend <strong><a href='index.php?view=$get_to' id='get_to' >$get_to</a></strong> $status</div>
	<div class='your_photo' >$your_photo <strong><a href='index.php?view=$user' >$user</a></strong></div>
	
<div class='no_float'></div></div> 

$area

<div class='input_dialog' $styleAuto ><h3>Все пользователи</h3>
	<div class='your_friends'>$new_mess $my_friendson $my_friendsof</div>
	<br />
	<input type='text' name='friend' $word id='search_field' onkeyup='search(this.value, 2)' value='$to_friend' /> 
			<div id='search_result'></div><span style='font-size: 12px'>*начните писать имя пользователя</span>
</div>
	
<div class='no_float'></div><div id='info'></div>



$funcLoad";

//---------------------------------------Активные диалоги-------------------
}
else
{
	$page_header = 'Диалоги';
	$content = infoStatus( 2, "Для того, чтобы зайти в Диалоги вам нужно зарегистрироваться или войти на сайт");
}
//Конец содержания страницы 

//Подсвечваем активный элемент меню 
$dialogues_active = "class='active'";
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
	
	require_once 'elements/htmlself.php';
?>
