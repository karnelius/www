<?php
	require_once 'elements/phpself.php';

if($loggedin)
{
	//приготовления
	
	$my_friends = 'нету:(';
	$sum = userNetwork($user);
	if(sizeof($sum['friends']))
	{
		foreach($sum['friends'] as $friend)
		{
			$stat = online($friend);
			if ($stat == 'offline') $my_friendsof = $my_friendsof. "<div class='dial_friend_p' ><div class='dial_friend_img' ><a href='?to=$friend'><img src='images/users/mini/$friend.jpg' alt='$friend' onerror='this.src = flash.def' /></a></div> <a class='dial_friend_text' href='?to=$friend'>$friend $stat</a> </div>";
			
			else $my_friendson = $my_friendson. "<div class='dial_friend_p' ><div class='dial_friend_img' ><a href='?to=$friend'><img src='images/users/mini/$friend.jpg' alt='$friend' onerror='this.src = flash.def' /></a></div> <a class='dial_friend_text' href='?to=$friend'>$friend $stat</a> </div>";
		}
	}
	
	
	
	//если пользователь пришел с гет адресата
	if(($_GET['to'] != '') && ($_GET['to'] != $user)) {
		$get_to = sanitizeString($_GET['to']);
		if ($get_to != '') {
			$photo_friend = showMini($get_to);
			$status = online($get_to);
		}
		$your_photo = showMini($user);
		//выводим
		require_once 'elements/html/dial_html.php';
		$content = $dial_top_links.$dial_top_photos.$dial_friend_link.$dial_area;
		$script = $dial_style;
		
	}
	else {
		require_once 'elements/html/dial_html.php';
		$content = $dial_top_links.$dial_friends;
		$script = $dial_style;	
	}
	
	
	
}


else
{
	$page_header = 'Диалоги';
	$content = infoStatus( 2, "Для того, чтобы зайти в Диалоги вам нужно зарегистрироваться или войти на сайт");
}



$dialogues_active = "class='active'";
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
	
	require_once 'elements/htmlself.php';
?>
