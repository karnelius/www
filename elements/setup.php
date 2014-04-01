<?php
require_once 'functions.php';
echo '<h3>Setting up</h3>'; //Настройка
createTable('members', 'user VARCHAR(16), pass VARCHAR(16), gender VARCHAR (16), age VARCHAR (16), mail VARCHAR(100), INDEX(user(6))');

createTable('messages', 'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, auth VARCHAR(16), recip VARCHAR(16), pm CHAR(1), time INT UNSIGNED, message VARCHAR(4096), INDEX(auth(6)), INDEX(recip(6))');

createTable('friends', 'user VARCHAR(16), friend VARCHAR(16), INDEX(user(6)), INDEX(friend(6))');

createTable('profiles', 'user VARCHAR(16), text VARCHAR(4096), INDEX(user(6))');
createTable('status', 'user VARCHAR(16), status VARCHAR(12), INDEX(user(6))');

//для Блога - статьи
createTable('articles', 'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, author VARCHAR(16), theme VARCHAR(4096), title VARCHAR(4096), time VARCHAR(32), preview TEXT, text TEXT, INDEX(id), INDEX(author(6))');

//для Блога - комментарии
createTable('comments', 'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, artid INT, author VARCHAR(16), time VARCHAR(32), text TEXT, INDEX(id), INDEX(author(6))');



?>