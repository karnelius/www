<?php
	require_once 'elements/phpself.php';	
	//Содержание страницы -----------------------------Page------	
			//регистрация
$fail = $newuser = $newpass = "";
if(isset($_SESSION['user'])) 
{
	destroySession();
	header("Location: registration.php");
}
if(isset($_POST['newuser'])) //Если в массиве пост есть переменная с индексом "newuser", запускаем шарманку. 
{
	//извлекаем данные из формы
	$newuser = sanitizeString($_POST['newuser']);
	$newpass = sanitizeString($_POST['newpass']);
	$newmail = sanitizeString($_POST['newmail']);
	$newgender = sanitizeString($_POST['newgender']);
	$captcha = sanitizeString($_POST['captcha']);
	//проверяем данные
	$fail = validate_username($newuser);
	$fail .= validate_password($newpass);
	$fail .= validate_email($newmail);
	$fail .= validate_gender($newgender);
	$fail .= validate_captcha($captcha);
	
	if ($fail == '')
	{
		$query = "SELECT * FROM members WHERE user='$newuser'";
        
		if(mysql_num_rows(queryMysql($query)))
		{
			$fail = "Такой логин уже испльзуется.<br /><br />";
         
		}
		else 
		{
			$query = "SELECT * FROM members WHERE mail='$newmail'";
			if(mysql_num_rows(queryMysql($query))) $fail = "Такой email уже испльзуется.<br /><br />";
			else {		
				$query = "INSERT INTO members (`user` ,`pass` ,`gender`, `mail` ) VALUES ('$newuser', '$newpass', '$newgender', '$newmail')";
				mysql_query($query); //пользователь зарегистрирован 
				header("Location: registration.php?on=$newuser");
				
			}
			
		}
		
	}
}			
$page_header = $page_title = 'Регистрация';
$not_reg = 1;

if(isset($_GET['on']))
{
	$newuser = sanitizeString($_GET['on']);
	$result = mysql_query("SELECT * FROM `members` WHERE `user`='$newuser'");
	$row = mysql_fetch_row($result);
	$content =infoStatus(1, "Регистрация прошла успешно")."<br />
<p>Логин: <strong>$row[0]</strong> </p>
<p>Пароль: <strong>$row[1]</strong> </p>
<p>Email: <strong>$row[5]</strong> </p>
<p>Пол: <strong>$row[2]</strong> </p>";

}
elseif(isset($_GET['remember']))
{
	$page_header = $page_title =  "Восстановление пароля";
	if ($_GET['remember'] != '') {
			$remember[1] = sanitizeString($_GET['remember']);
		  	$content = infoStatus(1,"Пароль от Вашей учетной записи  выслан по адресу <strong>$remember[1]</strong>");
	}
	else {
		
		if(isset($_POST['r_login']))
		{
			
			$r_login = sanitizeString($_POST['r_login']);
			$r_captcha = sanitizeString($_POST['r_captcha']);
			$fail = validate_captcha($r_captcha);
			
			if($fail == "")
			{
				
				$query = "SELECT `pass`, `mail` FROM `members` WHERE `user`='$r_login'";
				$result = mysql_query($query);
				$remember = mysql_fetch_row($result);
				$message = "Здравствйте $r_login! \n Высылаем вам запрашиваемые данные. \n Login: $r_login \n Password:$remember[0]";
				if (!$remember[1] == '')
				{
					$send =  mail($remember[1], "Восстановление пароля", $message, "From: support@alexchat.net\r");
					 header("Location: registration.php?remember=$remember[1]");
				}
			}
			$content =infoStatus(3, "Ошибка! Неправильно указан логин и/или неверно введен код с картинки.");
			
		}
		else {
		$content = "<p>Пароль будет выслан на тот email, который вы указали при регитрации. Если вы еще не регистрировались, то вам <a href='registration.php' >сюда</a>.</p><table border='0'  >
				<form method='post' action='' ><strong>$fail</strong>
				  <tr >
					<td>
						Логин:
					</td>
					<td> 	
						<input type='text' name='r_login' class='width190' id='search_field' onkeyup='search(this.value)'/> 1. Укажите свой логин
						<div id='search_result'></div>
					</td>
				  
				  </tr>
				  <tr >
					<td >
					<img id='captcha' src='elements/captcha.php' alt='защитный код' /><br /><a href='javascript: reloadCaptcha();' title='Сменить картинку' style='font-size:10px;' >Сменить картинку</a>
					</td>
					<td>
					<input type='text'  name='r_captcha' class='width190' /> 2. Введите код с картинки<br />
					</td> 
				  </tr>
				  <tr>
				  <td></td>
				  <td>
				  <input type='submit' class='submit' value='Восстановить пароль по Email' />
				  </td>
				</tr>
				
	</form>
	</table>";
		}
	}
}
else
{
	$content = infoStatus(2, "При регистрации испльзуйте только латинские символы")."
            <table border='0'  >
            <form method='post' action='' onsubmit='return validate(this)'><strong>$fail</strong>
              <tr >
              	<td>
                	Логин:
                </td>
                <td> 	
              		<input type='text' maxlength='20' class='width190' name='newuser' value='$newuser' onBlur='checkUser(this)' /><span id='info' ></span>
                </td>
              <tr>
              	<td>Пароль:</td>
                <td><input type='text' maxlength='16' class='width190' name='newpass' value='$newpass' /></td>
                </tr>
				<tr>
              	<td>Email:</td>
                <td><input type='text' class='width190'  name='newmail' value='$newmail' /></td>
                </tr>
              <tr>
              	<td>
                Пол:
                </td>
                <td>
                <label><input type='radio'  name='newgender' value='m' />M</label>|<label><input type='radio'  name='newgender' value='w' />Ж</label>|<label><input type='radio'  name='newgender' value='n' />Не знаю</label>
                </td> 
              </tr>
              <tr >
              	<td >
                <img id='captcha' src='elements/captcha.php' alt='защитный код' /><br /><a href='javascript: reloadCaptcha();' title='Сменить картинку' style='font-size:10px;' >Сменить картинку</a>
                </td>
                <td>
                <input type='text'  name='captcha' class='width190' /><br />
                </td> 
              </tr>
              <tr>
              <td></td>
              <td>
              <input type='submit' class='submit' value='Зарегистрироваться' />
              </td>
			</tr>
			
</form>
</table>";

}


$script = "<script src='js/validate.js' type='text/javascript' > </script>";

//Конец содержания страницы 

//Подсвечваем активный элемент меню 
$registration_active = "class='active'";
require_once 'elements/hmenu.php'; // Подключаем верхнее меню
//-------------------------------Собираем страницу--HTML----------------------
require_once 'elements/htmlself.php';
?>
