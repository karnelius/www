<?php
	require_once 'elements/phpself.php';
//Содержание страницы -----------------------------Page------
if($loggedin)
{
	$page_header = $user;
	$content = 'Привет, товарищ! здесь будет чат';
}
else
{
	$page_header = 'Чат';
	$content = 'Для того, чтобы зайти в чат вам нужно зарегистрироваться.';
}
//Конец содержания страницы 

//Подсвечваем активный элемент меню 
$chat_active = "class='active'";
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
	
	require_once 'elements/htmlself.php';
?>
