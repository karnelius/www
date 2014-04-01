<?php

class Server {
	/*
	 * Пишу свою историю событий в файл ready.log
	*/
	static function Log($message) {
		$f = fopen("ready.log", "a+");
		$date = date('Y.m.d  H:i:s');
		fwrite($f, $date." -//- ".$message."\r\n");
		fclose($f);
	}
		/*-------------------------------------------------------*/
		
	/*
	 * Стек действий для отправки клиенту в текущем запросе.
	 */
	static private $actions;
	
	/*
	 * Основная функция сервера. Сначала она удаляет переполненные файлы в папке socet/
	 * Разбирает запрос клиента. 
	 * Действие достает из суперглобального массива POST. Если значение строки совпадает с функциями в этом классе,
	 * она запускается как функция. Я проверял, так можно. После выполнения функции возвращамем клиенту ответ. 
	 */
	static function Run() {
		foreach (glob('sockets/*') as $sock) {
			if (filesize($sock) > 2048) {
				unlink($sock);
			}
		}
		$action = 'action'.$_POST['action'];
		if (is_callable('self::'.$action)) {
			self::$action();
			self::Send();
		}
	}
	
	/*
	 * Эта функция пишет действие в соккеты.
	 * Если передан параметр $self, то исключает указанный в этом параметре соккет.
	 */
	static function AddToSock($action, $params = '', $self = null) {
		foreach (glob('sockets/*') as $sock) {
			if ($self && strpos($sock, $self) !== false) {
				continue;
			}
			$f = fopen($sock, 'a+b') or die('socket not found');
			flock($f, LOCK_EX);
			fwrite($f, '{action: "'.$action.'", params: {'.$params.'}}'."\r\n");
			self::Log('{action: "'.$action.'", params: {'.$params.'}}'."\r\n");
			fclose($f);
		}
	}
	
	/*
	 * Эта функция добавляет действие в стек для отправки в текущем запросе.
	 */
	static function AddToSend($action, $params = '') {
		self::$actions[] = '{action: "'.$action.'", params: {'.$params.'}}';
	}
	
	/*
	 * Отправка стека действий на выполнение клиенту.
	 */
	static function Send() {
		if (self::$actions) {
			self::Log('{actions: ['.implode(', ', self::$actions).']}');
			exit('{actions: ['.implode(', ', self::$actions).']}');
		}
	}
	
	/*
	 * Действие.
	 * Соединение с сервером.
	 * Создает соккет и отправляет его идентификатор клиенту.
	 */
	static function actionConnect() {
		$sock = md5(microtime().rand(1, 1000));
		fclose(fopen('sockets/'.$sock, 'a+b'));
		self::AddToSock('Print', 'message: "Client connected."', $sock);
		self::AddToSend('Connect', 'sock: "'.$sock.'"');
		self::Log("Connect client: $sock -//- actionConnect()");
	}
	
	/*
	 * Действие.
	 * Отсоединение от сервера.
	 * Удаляет соккет.
	 */
	static function actionDisconnect() {
		$sock = $_POST['sock'];
		unlink('sockets/'.$sock);
		self::AddToSock('Print', 'message: "Client disconnected."');
		self::AddToSend('Disconnect');
		self::Log("Connect client: $sock -//- actionDisconnect()");
	}
	
	/*
	 * Действие.
	 * Отправляет введенные данные всем клиентам.
	 */
	static function actionSend() {
		$sock = $_POST['sock'];
		$data = htmlspecialchars(trim($_POST['data']), ENT_QUOTES);
		if (strlen($data)) {
			self::AddToSock('Print', 'message: "'.$data.'"', $sock);
			self::AddToSend('Print', 'message: "'.$data.'"');
			self::Log('Print '. 'message: "'.$data.'"', $sock);
		}
	}
	
	/*
	 * Действие.
	 * Слушает соккет до момента когда в нем появятся данные или же до истечения таймаута.
	 */
	static function actionRead() {
		$sock = $_POST['sock'];
		//self::Log('Start: ActionRead() with \'sock\': '.$sock);
		$time = time();
		while ((time() - $time) < 30) {
			if ((file_exists('sockets/'.$sock)) && ($data = file_get_contents('sockets/'.$sock))) {
				$f = fopen('sockets/'.$sock, 'r+b') or die('socket not found');
				flock($f, LOCK_EX);
				ftruncate($f, 0);
				fwrite($f, '');
				fclose($f);
				$data = trim($data, "\r\n");
				foreach (explode("\r\n", $data) as $action) {
					self::$actions[] = $action;
					self::Log('explode return \'action\': '.$action.'from '.$data);
				}
				self::Send();
			}
			usleep(250);
		}
	}
	
}
/*эта модель запустится только при обращении через ajax. *Если в суперглобальном массиве
 * $_SERVER будет об этом указано. Отправляем клиенту заголовки и запускаем сервер 
 * с функции Run()
 * Эта функция разбирает запрос клиента. 
 */ 
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	header('content-type: text/plain; charset=utf-8');
	//Server::Log('Call to serwer '.$_POST['sock'].' with action: '.$_POST['action']);
	Server::Run();
	
}

?>