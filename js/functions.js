// JavaScript Document

function redactVis ()
{
	if (document.getElementById('redact').style.display == 'none')
	{
	document.getElementById('redact').style.display = 'block';
	}
 else 
 	{
	 document.getElementById('redact').style.display = 'none';
	}
}

function online () 
{
	params = "online=yes";
	request = new ajaxRequest()
	request.open("POST", "elements/status.php", true)
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
request.setRequestHeader("Content-length", params.length)
request.setRequestHeader("Connection", "close")
request.onreadystatechange
request.send(params)
}

function ajaxRequest()
{
	try
	{
		var request = new XMLHttpRequest()
	}
	catch(e1)
	{
		try
		{
			request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e2)
		{
			try
			{
				request = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e3)
			{
				request = false;
			}
		}
	}
	return request;
}

function search(word, mode)
{
	if(word != '')
	{
		if(mode == 2)  params = "link="+word;
		else params = "word="+word;
	request = new ajaxRequest()
	request.open("POST", "elements/logins.php", true)
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
request.setRequestHeader("Content-length", params.length)
request.setRequestHeader("Connection", "close")
request.onreadystatechange = function()
{
	 if (this.readyState == 4) //дожидаемся завершения запроса
	{
		if(this.status == 200)
		{
			if(this.responseText != null)
			{
				document.getElementById('search_result').innerHTML = this.responseText //выводим ответ в блок messages
			}
			else alert("Ошибка Ajax: Данные не получены")
		}
		else alert("Ошибка Ajax: "+ this.statusText)
	}
}
request.send(params)
	}
	else document.getElementById('search_result').innerHTML = ''
}
function sel(name)
{
	document.getElementById('search_field').value = name
	document.getElementById('search_result').innerHTML = ''
}

function reloadCaptcha () {
 var random_value = new Date().getTime(); 
 document.getElementById('captcha').src = 'elements/captcha.php?random_value=' + random_value;

 }
function checkUser(user)
{
	if (user.value == '')
	{
		document.getElementById('info').innerHTML = '';
		return
	}
	params = "user=" + user.value;
	request = new ajaxRequest()
	request.open("POST", "elements/checkuser.php", true)
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
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
				document.getElementById('info').innerHTML = this.responseText
			}
			else alert("Ошибка Ajax: Данные не получены")
		}
		else alert("Ошибка Ajax: "+ this.statusText)
	}
}
request.send(params)
}
