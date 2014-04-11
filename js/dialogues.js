//эта функция будет удалена
function signal(string) {
				var dat = new Date();
				date = dat.getHours() + ':' + dat.getMinutes() + ':' + dat.getSeconds();
				if(document.getElementById('signal')) {
					document.getElementById('signal').innerHTML += date + ': '+string+'<br />';
					document.getElementById('signal').scrollTop = 999999;
				}
			}
			

/*************************************************/			
			$(document).ready(function() {
				
			myUser = document.getElementById('user') ? document.getElementById('user').innerHTML : false;
			myTo =  document.getElementById('get_to') ? document.getElementById('get_to').innerHTML : "";
				/*
				 * Настройка AJAX
				 */
				 if(myUser) {
				$.ajaxSetup({url: 'server.php', type: 'post', dataType: 'json'});
				//подключаемся к серверу сразу
				 }
				
				/*
				 * События кнопок и поля ввода
				 */
				 if($('#btnSend')) {
				$('#btnSend').click(user.Send);
				$('#input')
					.keydown(function(e){ if (e.keyCode == 13) { user.Send(); return false; } })
					.keypress(function(e){ if (e.keyCode == 13) { return false; } })
					.keyup(function(e){ if (e.keyCode == 13) { return false; } })
				;
				 }
				 user.Connect();
			});
			
			
			/*
			 * Печать сообщений в диалоге
			 */
			var log = {
				
				print: function(s) {
					if(document.getElementById('log')) {
					$('#log').append('<div>'+s+'</div>').get(0).scrollTop += 100;
					}
					//else alert(s);
				}
				
			};
			
			/*
			 * Печать/удаление окошка с ообщением снизу. 
			*/
			var flash = {
				show: function(params) {
					if(document.getElementById('dial_info')) 
					 flash.remove();
					document.body.innerHTML +="<div id='dial_info' ><div class='dial_info_head' ><img src='images/icon.png' style='float:left;' id='dial_ico' /><strong>Message</strong><img src='images/close_3-32.png' style='float:right;cursor:pointer;' onclick='flash.remove()'  /></div><div class='dial_info_cont'><a href='dialogues2.php?to="+params.user+"' ><strong><img src='images/users/mini/"+params.user+".jpg' alt='' style='float:left;' />"+params.user+": </strong>"+params.message+"</a></div></div>";
					//document.getElementById('myAudio').play();
					
				},
				remove: function() {
					elem = document.getElementById('dial_info');
					return elem.parentNode ? elem.parentNode.removeChild(elem) : elem;
					}
			};
			/*
			 * Действия присылаемые с сервера
			 */
			var actions = {
				
				Connect: function(params) {
					log.print('Sock: '+params.sock);
					user.sock = params.sock;
					user.conn = true;
					user.Read();
				},
				
				Print: function(params) {
					if(params.user == user.sock && params.to == user.to) {
						
						log.print('<div class="left" ><strong>'+params.user + '</strong>: '+params.message+'</div>');
					}
					else if(params.to == user.sock && params.user == user.to) {
						log.print('<div class="right" ><strong>'+params.user + '</strong>: '+params.message+'</div>');
					}
					else flash.show(params);
				
				},
				
				SystemPrint: function(params) {
					log.print('<span style="background:#FF4500;" >'+params.user + " to "+ params.to + ": "+ params.message+ " <small> "+params.date+"</small></span>");
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
				
				to: '',
				
				/*
				 * Эта функция обрабатывает приходящие с сервера действия и выполняет их.
				 */
				onSuccess: function(data) {
					signal('Запущена функция onSuccess() - обработка приходящих просов:'+data.actions[0]);
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
					signal('Запущена функция onComplete() - завершение ajax запроса');
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
					signal('Запущена функция onCompleteRead() - завершение слушания');
					
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
					signal('Запущена функция Connect() - присоединение к серверу');
					if (user.conn == false && user.busy == false) {
						user.busy = true;
						user.sock = myUser;
						user.to = myTo;
						$.ajax({
							data: 'action=Connect&user='+user.sock+'&to='+user.to,
							success: user.onSuccess,
							complete: user.onComplete
						});
						
					}
				},
				
				/*
				 * Действие.
				 * Отправка данных на сервер.
				 */
				Send: function() {
					signal('Запущена функция Send() - отправка данных на сервер');
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
					signal('Запущена функция Read() - прослушивание сокета');
					if (user.conn) {
						user.read = $.ajax({
							data: 'action=Read&sock='+user.sock,
							success: user.onSuccess,
							complete: user.onCompleteRead
						});
					}
					
				}
				
			};
			