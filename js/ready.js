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
				$.ajaxSetup({url: 'server.php', type: 'post', dataType: 'json'});
				//подключаемся к серверу сразу
				 user.Connect();
			});
			
			
			/*
			 * Печать сообщений в диалоге
			 */
			var log = {
				
				print: function(s) {
					if(document.getElementById('log')) {
					$('#log').append('<div>'+s+'</div>').get(0).scrollTop += 1000000;
					}
					//else alert(s);
				}
				
			};
			
			/*
			 * Печать/удаление окошка с ообщением снизу. 
			*/
			var flash = {
				def: 'images/users/mini/default.jpg',
				
				//Действия
				show: function(params) {
					if(document.getElementById('dial_info')) 
					 	flash.remove();
					document.body.innerHTML +="<div id='dial_info' ><div class='dial_info_head' ><img src='images/icon.png' style='float:left;' id='dial_ico' /><strong>Message</strong><img src='images/close_3-32.png' style='float:right;cursor:pointer;' onclick='flash.remove()'  /></div><div class='dial_info_cont'><a href='dialogues.php?to="+params.user+"' ><strong><img src='images/users/mini/"+params.user+".jpg' alt='' style='float:left;' onerror='this.src = flash.def' />"+params.user+": </strong>"+params.message+"</a></div></div>";
					//document.getElementById('myAudio').play();
					if(document.getElementById('log')) {
						document.getElementById('log').scrollTop += 1000000;
					}
				},
				
				remove: function() {
					elem = document.getElementById('dial_info');
					return elem.parentNode ? elem.parentNode.removeChild(elem) : elem;
					},
				play: function() {
					var audio = document.getElementById('myAudio');
					try {
						
						if (audio.canPlayType("audio/mpeg") || audio.canPlayType("audio/ogg")) {
							
							audio.play();
						}
					} catch(e) {
						
						  return true;
						
					}

					
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
				Mark: function (params) {
					if(document.getElementById(params.id))
						document.getElementById(params.id).className = "left_read";
					//меняем внешний вид по ID сообщения. работаем через класс
				},
				Print: function(params) {
					//signal('Запущена функция Print() - печать сообщения:');
					if(params.user.toUpperCase() == user.sock.toUpperCase() && params.to.toUpperCase() == user.to.toUpperCase()) {
						//я отправил
						if(params.read == 0)
							log.print('<div class="left_unread" id="'+params.id+'" ><strong>'+params.user + '</strong>: '+params.message+'</div>');
						else
							log.print('<div class="left_read" id="'+params.id+'" ><strong>'+params.user + '</strong>: '+params.message+'</div>');
					}
					
					
					else if(params.to.toUpperCase() == user.sock.toUpperCase() && params.user.toUpperCase() == user.to.toUpperCase()) { 
						//я получил
						log.print('<div class="right" id="'+params.id+'" ><strong>'+params.user + '</strong>: '+params.message+'</div>');
						flash.play();
					}
					else { 
						flash.show(params);
						flash.play();
					}
				
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
				signal: false,
				to: '',
				
				/*
				 * Эта функция обрабатывает приходящие с сервера действия и выполняет их.
				 */
				onSuccess: function(data) {
					signal('Запущена функция onSuccess() - обработка приходящих запросов:'+data.actions[0]);
					if (typeof data.actions == 'object') {
						for (var i = 0; i < data.actions.length; i++) {
							if (typeof actions[data.actions[i].action] == 'function') {
								//signal('1-Обработка в этом месте onSuccess():'+data.actions[i].action+' ' +data.actions[i].params.id);
								actions[data.actions[i].action](data.actions[i].params);
								//signal('2-Обработка в этом месте onSuccess():'+data.actions[i].action);
							}
						}
						user.signal = true; //после первой загрузки при коннекте включаем звук
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
							data: 'action=Connect&user='+user.sock+'&to='+user.to+'&lastid='+user.lastid,
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
						//есил были изменения id, добавляем в data переменную lastId
						user.read = $.ajax({
							data: 'action=Read&sock='+user.sock+'&to='+user.to,
							success: user.onSuccess,
							complete: user.onCompleteRead
						});
					}
					
				}
				
			};
			