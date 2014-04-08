<?php
require_once '../elements/phpself.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	
	<head>
		<title>Socket App Client</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="author" content="Melnaron" />
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<style type="text/css">
			
			#log {
				border: 1px solid #999999;
				height: 250px;
				overflow: auto;
				margin: 10px 0;
				padding: 10px;
			}
			#signal {
				width:600px;
				height:200px;
				margin:10px auto;
				background:#CCC;
				font-family:"Courier New", Courier, monospace;
				font-size:12px;
				overflow: auto;
				border:2px solid #999;
				border-radius: 5px;
				padding: 3px;
			}
			#signal p {
				font-size:14px;
				margin-top: 0;
				text-align:center;
				font-weight: bold;
			}
		</style>
		<script type="text/javascript" src="lib/jquery.js"></script>
		<script type="text/javascript">
			function myDate() {
				var dat = new Date();
				document.getElementById('signal').scrollTop = 999999;
				return  dat.getHours() + ':' + dat.getMinutes() + ':' + dat.getSeconds();
			}
			
			$(document).ready(function() {
				
				/*
				 * Настройка AJAX
				 */
				$.ajaxSetup({url: 'server.php', type: 'post', dataType: 'json'});
				
				//подключаемся к серверу сразу
				user.Connect();
				
				/*
				 * События кнопок и поля ввода
				 */
				$('#btnConnect').click(user.Connect);
				$('#btnDisconnect').click(user.Disconnect);
				$('#btnSend').click(user.Send);
				$('#input')
					.keydown(function(e){ if (e.keyCode == 13) { user.Send(); return false; } })
					.keypress(function(e){ if (e.keyCode == 13) { return false; } })
					.keyup(function(e){ if (e.keyCode == 13) { return false; } })
				;
				
			});
			
		</script>
		<script type="text/javascript">
			
			/*
			 * Лог
			 */
			var log = {
				
				print: function(s) {
					$('#log').append('<div>'+s+'</div>').get(0).scrollTop += 100;
				}
				
			};
			
			/*
			 * Действия присылаемые с сервера
			 */
			var actions = {
				
				Connect: function(params) {
					log.print('Connected.'+params.date);
					log.print('Sock: '+params.sock);
					user.sock = params.sock;
					user.conn = true;
					user.Read();
				},
				
				Disconnect: function(params) {
					log.print('Disconnected.'+params.date);
				},
				
				Print: function(params) {
					log.print(params.user + " to "+ params.to + ": "+ params.message+ " <small> "+params.date+"</small>");
				}
				
			};
			
			/*
			 * Пользователь (клиент)
			 */
			var user = {
				
				sock: null,
				
				conn: false,
				
				busy: false,
				
				read: null,
				
				to: "<?php echo $_GET['to']; ?>",
				
				/*
				 * Эта функция обрабатывает приходящие с сервера действия и выполняет их.
				 */
				onSuccess: function(data) {
					document.getElementById('signal').innerHTML += myDate() +' :Запущена функция onSuccess() - обработка приходящих просов:'+data.actions[0].params.message+'<br />';
					if (typeof data.actions == 'object') {
						for (var i = 0; i < data.actions.length; i++) {
							if (typeof actions[data.actions[i].action] == 'function') {
								actions[data.actions[i].action](data.actions[i].params);
							}
						}
					}
				},
				
				/*
				 * Эта функция выполняется по завершении ajax-запроса.
				 */
				onComplete: function(xhr) {
					document.getElementById('signal').innerHTML += myDate() +' :Запущена функция onComplete() - завершение ajax запроса:<br />';
					if (xhr.status == 404) {
						actions.Disconnect();
					}
					user.busy = false;
				},
				
				/*
				 * Эта функция выполняется по завершении запроса-слушания.
				 * При удачном завершении запроса (==200) моментальное возобновление прослушивания соккета.
				 * При неудачном (!=200) возобновление через 5 секунд.
				 */
				onCompleteRead: function(xhr) {
					document.getElementById('signal').innerHTML += myDate() +' :Запущена функция onCompleteRead() - завершение слушания:<br />';
					if (xhr.status == 200) {
						user.Read();
					} else {
						setTimeout(user.Read, 5000);
					}
				},
				
				/*
				 * Действие.
				 * Соединение с сервером.
				 */
				Connect: function() {
					document.getElementById('signal').innerHTML += myDate() +' :Запущена функция Connect() - присоединение к серверу:<br />';
					if (user.conn == false && user.busy == false) {
						log.print('Connecting...');
						user.busy = true;
						$.ajax({
							data: 'action=Connect&user=<?php echo $user; ?>'+'&to='+user.to,
							success: user.onSuccess,
							complete: user.onComplete
						});
					}
				},
				
				/*
				 * Действие.
				 * Отсоединение от сервера.
				 */
				Disconnect: function() {
					document.getElementById('signal').innerHTML += myDate() +' :Запущена функция Disconnect() - отсоединение от сервера:<br />';
					if (user.conn && user.busy == false && user.read) {
						log.print('Disconnecting...');
						user.busy = true;
						$.ajax({
							data: 'action=Disconnect&sock='+user.sock,
							success: user.onSuccess,
							complete: user.onComplete
						});
						user.sock = null;
						user.conn = false;
						user.read.abort();
					}
				},
				
				/*
				 * Действие.
				 * Отправка данных на сервер.
				 */
				Send: function() {
					document.getElementById('signal').innerHTML += myDate() +' :Запущена функция Send() - отправка данных на сервер:<br />';
					if (user.conn) {
						var data = $.trim($('#input').val());
						//var to = $.trim($('#to').val());
						if (!data) {
							return;
						}
						$.ajax({
							data: 'action=Send&sock='+user.sock+'&data='+data+'&to='+user.to,
							success: user.onSuccess,
							complete: user.onComplete
						});
						$('#input').val('');
					} else {
						log.print('Please connect.');
					}
				},
				
				/*
				 * Действие.
				 * Прослушивание соккета.
				 */
				Read: function() {
					document.getElementById('signal').innerHTML += myDate() +' :Запущена функция Read() - прослушивание сокета:<br />';
					if (user.conn) {
						user.read = $.ajax({
							data: 'action=Read&sock='+user.sock,
							success: user.onSuccess,
							complete: user.onCompleteRead
						});
					}
					
				}
				
			};
			
		</script>
	</head>
	
	<body>
    <?php echo "Пользователь ".$user."<br />"; ?>
		<input id="btnConnect" type="button" value="Connect" />
		<input id="btnDisconnect" type="button" value="Disconnect" />
		<div id="log"></div>
		message:<input id="input" type="text" /> <br />
        name:<input id="name" type="text" value="<?php echo $user; ?>" /><br />
		<input id="btnSend" type="button" value="Send" />
        <div id="signal" ><p>Консоль</p></div>
	</body>
	
</html>