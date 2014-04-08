<?php
/*
 * Чат основан на статье http://habrahabr.ru/post/41223/
 */
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
	 * Эта функция пишет действие в соккеты. Пишет адресату $to
	 */
	static function AddToSock($action, $params = '', $to) {
		if(file_exists('sockets/'.$to)) {
			$f = fopen('sockets/'.$to, 'a+b') or die('socket not found');
			flock($f, LOCK_EX);
			fwrite($f, '{action: "'.$action.'", params: {'.$params.'}}'."\r\n");
			self::Log('ADDSOCK{action: "'.$action.'", params: {'.$params.'}}'."\r\n");
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
			//self::Log('{actions: ['.implode(', ', self::$actions).']}');
			exit('{actions: ['.implode(', ', self::$actions).']}');
		}
	}
	
	/*
	 * Действие.
	 * Соединение с сервером.
	 * Создает соккет и отправляет его идентификатор клиенту.
	 */
	static function actionConnect() {
		$sock = $_POST['user'];
		$to = $_POST['to'];
		if(!file_exists('sockets/'.$sock)) {
			fclose(fopen('sockets/'.$sock, 'a+b'));
		}	
		self::AddToSock('Print', 'message: "Client connected.", date: "12.04.2014"', $sock);
		self::AddToSend('Connect', 'sock: "'.$sock.'", date: "12.04.2014"');
		//идем в базу за сообщениями
		require_once '../elements/base.php';
		@$result = mysql_query("SELECT * FROM `messages` WHERE `sender`='$sock' AND `recipient`='$to' OR `sender`='$to' AND `recipient`='$sock' ORDER BY `id`");
		if($result) {
			$rows = mysql_num_rows($result);
			for ($i = 0; $i < $rows; ++$i) {
				$row = mysql_fetch_row($result);
				self::AddToSend('Print', 'user: "'.$row[1].'", message: "'.$row[3].'", to: "'.$row[2].'", date: "'.$row[5].'"');
			}
		}
		self::Log("Connect client: $sock -//- actionConnect()"); 
		
	}
	
	/*
	 * Действие.
	 * Отсоединение от сервера.
	 * Удаляет соккет.
	 */
	 //функция будет удалена
	static function actionDisconnect() {
		$sock = $_POST['sock'];
		unlink('sockets/'.$sock);
		self::AddToSock('Print', 'message: "Client disconnected.", date: "12.04.2014"');
		self::AddToSend('Disconnect', 'date: "12.04.2014"');
		self::Log("Connect client: $sock -//- actionDisconnect()");
	}
	
	/*
	 * Действие.
	 * Отправляет введенные данные всем клиентам.
	 */
	 //разбирает POST, отправляет сообщения адресату и отправителю. 
	static function actionSend() {
		$sock = $_POST['sock'];
		$to = htmlspecialchars(trim($_POST['to']), ENT_QUOTES);
		$date = date('Y.m.d  H:i:s');
		$data = htmlspecialchars(trim($_POST['data']), ENT_QUOTES);
		//добавляем в базу и в сокет получателю
		if (strlen($data) && isset($to)) {
			require_once '../elements/base.php';
			$result = mysql_query("INSERT INTO `messages` VALUES (NULL, '$sock', '$to', '$data', '', '$date', 0, 0)");
			self::AddToSock('Print', 'user: "'.$sock.'", message: "'.$data.'", to: "'.$to.'", date: "'.$date.'"', $to);
			self::AddToSend('Print', 'user: "'.$sock.'", message: "'.$data.'", to: "'.$to.'", date: "'.$date.'"');
			self::Log('Print(log) '. 'user: "'.$sock.'", message: "'.$data.'", to: "'.$to.'", date: "'.$date.'"', $sock);
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
		if(file_exists('sockets/'.$sock)){
			while ((time() - $time) < 30) {
				if ($data = file_get_contents('sockets/'.$sock)) {
					$f = fopen('sockets/'.$sock, 'r+b') or die('socket not found');
					flock($f, LOCK_EX);
					ftruncate($f, 0);
					fwrite($f, '');
					fclose($f);
					$data = trim($data, "\r\n");
					foreach (explode("\r\n", $data) as $action) {
						self::$actions[] = $action;
					}
					self::Send();
				}
				usleep(250);
			}
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