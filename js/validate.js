// функции для проверки регистрационной формы

function validate(form)
{
	var fail = ''
	fail = validateUsername(form.newuser.value)
	fail += validatePassword(form.newpass.value)
	fail += validateEmail(form.newmail.value)
	fail += validateGender(form.newgender.value)
	fail += validateCaptcha(form.captcha.value)
	if (fail == "") return true
	else 
	{ 
		alert(fail); 
		return false 
	}
}
 

function validateUsername(field)
{
	if (field == "") return "Не введено имя пользователя. \n"
	else if(field.length < 3) return "В имени пользователя дожлно быть не менее 3 символов. \n" 
	else if (/[^\w]/.test(field)) return "Имя пользотеля должно состоять только из латинских букв, цифр и символа \"_\" .\n"
	return ""
}
function validatePassword(field)
{
	if (field == "") return "Пароль не введен. \n"
	else if (field.length < 4) return "Пароль не может быть короче 4-и символов \n"
	else if (/[^\w]/.test(field)) return "Пароль должен состоять только из латинских букв, цифр и символа \"_\" .\n"
	else return ""
}

function validateEmail(field)
{
	if (field == "") return "Email не указан. \n"
	else  if (!(/[\w\.]+@\w+\.\w+/.test(field))) return "Email не соответствует формату. \n"
	
	return ""
}

function validateGender(field)
{
	if (field == "") return "Пол не указан. \n"
	else return ""
}
function validateCaptcha(field)
{
	if ( field == "") return "Защитный код не указан. \n"
	
	else return ""
}