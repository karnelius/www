// JavaScript Document
var lastId = 0; //счетчик входящих
var newLastId = 0; //считает недавние сообщения. сравнивает со счетчиком входящих
var numNew = 0; //количество новых сообщений в <title>
var maxId = 0; // максимальный id. возвращаем его на сервер


//функции подгрузки сообщений 

function send()
{
var mess = document.getElementById('mess_to_send').value //получаем текст сообщения
var	recip = document.getElementById('get_to').innerHTML //берем имя того, кому мы пишем. 

	a = {'recip' : recip , 'mess' : mess }; //собираем джейсон для отправки

	var aString = JSON.stringify(a);	//делаем джейсон строкой
	params = 'mess=' +aString; //собираем строку запроса для отправки на add_mess.php
	
	
	request = new ajaxRequest()
	request.open('POST', 'elements/add_mess.php', true)
	request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
request.onreadystatechange = function()
{
	 if (this.readyState == 4) //дожидаемся завершения запроса
	{
		if(this.status == 200)
		{
			if(this.responseText != null)
			{
				document.getElementById('info').innerHTML = this.responseText //выводим ответ в блок messages
			
			document.getElementById('mess_to_send').value = ''	//очищаем поле ввода сообщений
			//load_messes() //включаем функция загрузки сообщений из базы
			}
			else alert('Ошибка Ajax: Данные не получены')
		}
		else alert('Ошибка Ajax: Мир не совершенен :('+ this.statusText)
	}
}
request.send(params)

}






//функция загрузки сообщений

	function load_messes()
	{
	var chatBox = document.getElementById('messages');
	var get_to = document.getElementById('get_to').innerHTML
	var params = 'to='+get_to+'&id='+maxId; //запрос прост
	request = new ajaxRequest()
	request.open('POST', 'elements/load_messes.php', true)
	request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
	request.setRequestHeader("Content-length", params.length)
request.setRequestHeader("Connection", "close")
request.onreadystatechange = function()
{
	 if (this.readyState == 4) 
	{
		if(this.status == 200)
		{
			if(this.responseText != null)
			{
				var mystring = this.responseText //получаем джейсон с сообщениями
				
				text = JSON.parse(mystring);  
				answer = ''
			
				for (i = 0 ; i < text.length; ++i)
				{
					if(text[i]['author'].toUpperCase()==get_to.toUpperCase()) { //мне письмо 
					answer += '<div class="author_mess" ><strong>'+ text[i]['author']
					+  '</strong>: ' + text[i]['message'] + '</div>';
					newLastId = text[i]['id'];	//если от собеседника еще одно письмо
					}
					else { //от меня
					//не прочитанное:
					if(text[i]['status'] == 0) {
						 answer += '<div class="recip_mess no_read" ><strong>' + text[i]['author'] 
						 +'</strong>: '+text[i]['message'] + '</div>';
						 
					}
					//от меня прочитанное
					else {
							answer += '<div class="recip_mess" ><strong>'+ text[i]['author']
							+'</strong>: ' + text[i]['message'] + '</div>';
							
					}
					
					}
				maxId = text[i]['id'];	
				}
				if((newLastId > lastId) && (lastId != 0)) {
					numNew = numNew + 1;
					document.getElementById('myAudio').play();
					document.title = "+"+ numNew +" Диалоги";
					lastId = newLastId;
				}
				else { 
					lastId = newLastId;
				}
				//если пришли новые сообщения, подгружаем только их и прокручиваем вниз
				if(answer != '') {
					chatBox.innerHTML = chatBox.innerHTML + answer //в блок messages помещаем ответ
					chatBox.scrollTop = 999990; //фигачим полосу прокрутки вниз. 
				}
			}
			else alert('Ошибка Ajax: Данные не получены')
		}
		else alert('Ошибка Ajax: '+ this.statusText)
	}
}
request.send(params)
	}
	
	function clearNews() {
		if (document.title != "Диалоги") {
		document.title = "Диалоги";
		numNew = 0;
		}
	}