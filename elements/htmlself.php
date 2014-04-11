<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>
<link type="text/css" rel="stylesheet" href="styles/main.css" />
<link rel="shortcut icon" href="images/icon.ico" type="image/x-icon" />
<!--[if IE]> <link rel="stylesheet" type="text/css" href="styles/ie_wtf.css" /> <![endif]-->
<script src="js/jquery.js" type="text/javascript" > </script>
<script src="js/functions.js" type="text/javascript" > </script>
<script src="js/dialogues2.js" type="text/javascript" > </script>

<?php echo $script; ?>


</head>

<body>
<div class='auto_header' >
		<h1 class='logo_name' >Alex chat</h1>
        <h2 class='logo_text' >Своя сеть, с Блек-Джеком и шлюхами</h2>
    </div><!-- /'auto_header' -->
<?php echo $hmenu; ?>
<div class="page">
        <div class="page_left">
            
<h2 class='page_header' ><?php echo $page_header; ?></h2>
            <div class='page_content' ><?php echo  $content, $post_content, $post_content2; ?></div><!-- /'page_content' -->
    </div><!-- /'page_left' -->
			
         
        <div class="page_right">
        <div class='login_form'>
			<?php
			if ($not_reg != 1)
            echo $login_form;
            ?>
				<ul>
					<li>
						<a href="registration.php" title="Create a new user account.">Создать новый аккаунт</a>
					</li>
					<li>
						<a href="registration.php?remember" title="Request new password via e-mail.">Вспомнить пароль</a>
					</li>
				</ul>
            </div><!-- /'login_form' -->
         
            <div class="right_text">
				<h3><?php echo $random_article_header; ?></h3>
				<p><?php echo $random_article; ?></p>
			</div><!-- /'right_text' -->
            <div class="search" >
				<h3>Поиск</h3>
				<form action="" method="post" >
					<input type="text" name="search" size="25" class='width190' />
					<input type="submit" value="Найти" class="submit" />
				</form>
            </div><!-- /'search' -->
            
        </div><!-- /'page_right' --> 
		<div class="no_float"></div>
            <div class="footer">
                <div class="footer_text" >
                    <p><a href="index.php" id='user' ><?php echo $user; ?></a> | <a href="about.php">О сайте</a> | <a href="test.php">Тест</a></p>
                    <p>Bender © 2013. All Rights Reserved.</p>
              </div><!-- /'footer_text' -->
            </div><!-- /'rooter' -->
</div><!-- /'page' -->
<?php echo $onlineScript; ?>
<audio id='myAudio'  >
<source src='media/button1.mp3' type='audio/mpeg'  />
<source src='media/button1.wav' type='audio/wav'  /> 
</audio>
</body>
</html>
