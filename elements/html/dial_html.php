<?php
$dial_top_links = "<div class='mess_links'><div class='mess_links_inbox $in_active' ><h3><a href='messages.php?inbox=1' >Входящие $is_new_mess</a></h3></div><div class='mess_links_outbox $out_active' ><h3><a href='messages.php?outbox=1' >Исходящие</a></h3></div><div class='mess_links_new $new_active' ><h3><a href='messages.php?new' >Новое*</a></h3></div><div class='mess_links_dial mess_links_active' ><h3><a href='dialogues.php' >Диалоги</a></h3></div></div>";

$dial_top_photos = "<div class='dial_photos' >
	<div class='mailto'>$photo_friend <strong><a href='index.php?view=$get_to' id='get_to' >$get_to</a></strong> $status</div>
	<div class='your_photo' >$your_photo <strong><a href='index.php?view=$user' >$user</a></strong></div>
	<div class='no_float'></div></div>";
	
$dial_friend_link = "<a href='dialogues.php' id='link_friends' >Список друзей</a>";

$dial_area = '<div id="log"></div>
		<div class="dial_under" >
		<form action="javascript: user.Send();" />
		<input id="input" size="50"  type="text" />
		<input id="btnSend"  type="button" value="Отправить" onclick="user.Send()" />
		</form>
		</div>
		<br />
		<div id="signal" ><p>Консоль</p></div>';
		
$dial_friends = "<div class='input_dialog' $styleAuto ><h3>Все пользователи</h3>
	<div class='your_friends'>$new_mess $my_friendson $my_friendsof</div>
	<br />
	<input type='text' name='friend' $word id='search_field' onkeyup='search(this.value, 2)' value='$to_friend' /> 
	<div id='search_result'></div><span style='font-size: 12px'>*начните писать имя пользователя</span>
	</div>
	<div class='no_float'></div><div id='info'></div>";

$dial_style = "<link type='text/css' rel='stylesheet' href='styles/dialogues.css' />";
?>