<?php

$sum = userNetwork($view); //запрашиваем двумемрный массив данными по друзьям, подписчикам и запросам на добавления в друзья

if(sizeof($sum['friends']))
{
	$post_content = "<br /><div class='friend_list' ><b>Друзья пользователя</b><ul>";
	foreach($sum['friends'] as $friend)
	{
		$stat = online($friend);
		$mini = showMini($friend);
		if($update_link) $friend_remove = "[<a href='?remove=$friend'>удалить</a>]"; 
		$post_content = $post_content. "<li class='pattern'>$mini<a href='index.php?view=$friend'> $friend</a> $stat <br /> $friend_remove <a href='messages.php?new=$friend' class='mess_a' title='Отправить сообщение' ><img src='images/mail.png' alt='' /></a></li>";
	}
	$post_content = $post_content. "</ul></div><!--/.friend_list-->";
	$friends = TRUE;
}
else $post_content =  "<br /><div class='friend_list' ><b>Друзья пользователя</b>".infoStatus(2, "Нет ни одного друга")."</div><!--/.friend_list-->";
//----------------<!--/.friend_list-->

if(sizeof($sum['followers']))
{
	$post_content2 =  "<b>Подписчики пользователя</b><ul>";
	foreach($sum['followers'] as $friend)
	{
		$stat = online($friend);
		$mini = showMini($friend);
		if ($update_link) $follower_add = "[<a href='?add=$friend'>добавить</a>]";
		$post_content2 = $post_content2. "<li class='pattern'>$mini<a href='index.php?view=$friend'> $friend</a> $stat <br />$follower_add <a href='messages.php?new=$friend' class='mess_a' title='Отправить сообщение' ><img src='images/mail.png' alt='' /></a></li>";
	}
	$post_content2 = $post_content2. "</ul>";
	$friends = TRUE;
}
else $post_content2 = "<b>Подписчики пользователя</b>".infoStatus(2, "Нет ни одного подпичика"). "<br />";


if(sizeof($sum['following']))
{
	$post_content3 =  "<b>Пользователь предложил дружбу</b><ul>";
	foreach($sum['following'] as $friend)
	{
		$stat = online($friend);
		$mini = showMini($friend);
		if ($update_link) $i_follower = "[<a href='?remove=$friend'>отменить</a>]";
		$post_content3 = $post_content3. "<li class='pattern'>$mini<ol class='pattern' ><li><a href='index.php?view=$friend'   > $friend</a> $stat </li><li>$i_follower <a href='messages.php?new=$friend' class='mess_a' title='Отправить сообщение' ><img src='images/mail.png' alt='' /></a></li></ol></li>";
	}
	$post_content3 = $post_content3. "</ul>";
	$friends = TRUE;
}
else $post_content3 = "<b>Пользователь предожил дружбу</b>".infoStatus(2, "Нет ни одной заявки на добавление в друзья");
$post_content2 = "<div class='followers_list' >$post_content2 $post_content3 </div>";
?>