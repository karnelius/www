<?php
	require_once 'elements/phpself.php';
//Содержание страницы -----------------------------Page------

$page_header = $page_title = "О сайте";
$content = infoStatus(2, "Здесь будет содержимое страницы");
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
	
	require_once 'elements/htmlself.php';
?>
