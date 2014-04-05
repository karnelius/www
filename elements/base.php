<?php
$dbhost = 'localhost';
 $dbname = 'alexdb'; // Необходимо создань новую базу данных, нового пользователя и пароль. 
 $dbuser = 'alex';
 $dbpass = 'alexpass';
 $appname = 'Чат для Alex';
 
/*$dbhost = 'db02.hostline.ru';
 $dbname = 'vh157777_alexdb'; // Необходимо создань новую базу данных, нового пользователя и пароль. 
 $dbuser = 'vh157777_alex';
 $dbpass = '2363669al';
 $appname = 'Чат для Alex';*/
 
 
 mysql_connect($dbhost, $dbuser, $dbpass) or die( "Ошибка в позиции 1".mysql_error()); // Подключаемся к базе данных
 mysql_select_db($dbname) or die(" Ошибка в позиции 2".mysql_error()); // Выбираем саму базу данных
 
 ?>