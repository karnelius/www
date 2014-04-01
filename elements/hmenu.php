<?php
// "Какое меню будем выводить?" - спросишь ты
if ($loggedin)
{
 
 
$hmenu = <<<_END
<div class='top_menu' >
    	<ul class='hmenu'>
			<li>
				<a href='index.php' $index_active >Моя страница</a>
			</li>		
			<li>
				<a href='members.php?view=$user' $members_active >Участники</a>
			</li>
			<li>
				<a href='blog.php?page=1' $blog_active >Блог</a>
				<ul>
					<li>
						<a href='blog.php?author=$user'>Мои статьи</a>
					</li>
						<li>
						<a href='blog.php?page=1'>Все статьи</a>
					</li>
					<li>
						<a href='blog.php?new=true'>Создать статью</a>
					</li>
				</ul>
			</li>
			<li>
				<a href='messages.php?inbox=1' $messages_active>Сообщения $is_new_mess</a>
				<ul>
					<li>
				<a href='dialogues.php'>Диалоги</a>
					</li>
					<li>
				<a href='messages.php?inbox=1' >Входящие $is_new_mess</a>
					</li>
					<li>
				<a href='messages.php?outbox=1' >Исходящие</a>
					</li>
					<li>
				<a href='messages.php?new' >Новое сообщение</a>
					</li>
				</ul>
			</li>
				
		</ul>
    </div><!-- /'top_menu' -->
_END;
}
else
{
	$hmenu = <<<_END
<div class='top_menu' >
    	<ul class="hmenu">
			<li>
				<a href='index.php' $index_active >На главную</a>
			</li>	
			<li>
				<a href='registration.php' $registration_active >Регистрация</a>
			</li>
			
			<li>
				<a href='blog.php?page=1' $blog_active >Блог</a>
				<ul>
					
						<li>
						<a href='blog.php?page=1'>Все статьи</a>
					</li>
					
				</ul>
			</li>
			<li>
				<a href='messages.php' $messages_active>Сообщения</a>
			</li>	
			
		</ul>
    </div><!-- /'top_menu' -->
_END;
}
//Конец выбора меню
?>