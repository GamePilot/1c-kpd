<?php

/**
* Тут будут собраны все мои функции для дебага
* @version 1.16 (07 апреля 2014 года)
*/
class MyDebug {

	/**
	 * Адрес файла с логом по умолчанию без $_SERVER["DOCUMENT_ROOT"]
	 */
	const LINK_LOG = "/upload/damp_log.txt";

	/**
	 * Адрес файла с логами базы SQLite по умолчанию без $_SERVER["DOCUMENT_ROOT"]
	 */
	const LINK_LOG_DB = "/upload/my_log.sqlite";

	/**
	 * Массив ошибок
	 * @var array
	 */
	public static $error = array();
	
	/**
	 * Масссив сообщений
	 * @var array
	 */
	public static $mess = array();

	/**
	 * Выбрасываль ли исключение php, вместо записи в массив ошибок
	 * @var bool
	 */
	public static $exception = false;
	
    /**
     * Запись лога в базу данных SQLite
	 * @param string $table_name Имя таблицы с логами (только анг. буквы без пробелов!)
     * @param mixed $log1 Строка или дамп массива (колонка 1)
	 * @param mixed $log2 Строка или дамп массива (колонка 2)
	 * @param mixed $log3 Строка или дамп массива (колонка 3)
     * @param string $file Обсалютная ссылка на файл базы (По умолчанию /upload/my_log.sqlite)
	 * @param bool $exception Если true, массив с ошибками self::$error[] будет вываливаться как Exception
     * @return true or false
	 * @throws Exception
     */
	public static function log_to_SQLite($table_name, $log1 = "", $log2 = "", $log3 = "", $exception = false, $file=self::LINK_LOG_DB)
	{
		$file = $_SERVER["DOCUMENT_ROOT"].$file;
		$go = true;
		
		for($i = 1; $i <= 3; $i++){
			$name = "log".$i;
			if(is_array($$name)){
				$$name = "<pre>".print_r($$name, true)."</pre>";
			}
			$$name = (string) $$name;
		}
		
		if(!$table_name){
			self::$error[] = "Не задано имя таблицы";
			$go = false;
		}
		
		// Если файла с базой еще нет, создаем
		if(!is_file($file)){
			if(file_put_contents($file, "") === false){
				self::$error[] = "Невозможно создать файл с базой, вероятно проблема с правами на запись по адресу ".$file;
				$go = false;
			}
		}
		
		if($go === true)
		{	
			// Создаем объект базы
			$db = new PDO("sqlite:$file");
			$table_name = $db->quote($table_name);  // Для безопасности данных
		
			/* Проверка существования таблиц */
		
			//Проверка существования индексной таблицы
			$sql = "SELECT id, table_name FROM index_table LIMIT 1";
			if(!$db->query($sql)){
				self::$error[] = "Нет индексной таблицы";
				// Создаем индексную таблицу
				self::create_index_table($db);
				// Создаем таблицу с логами
				self::create_log_table($table_name, $db);
				// Записываем туда лог
				self::create_log_string($table_name, $db, $log1, $log2, $log3);
				// Ошибки
				self::$error[] = $db->errorInfo();
			}
		
			// Проверим есть ли таблица с логами
			elseif(!self::check_log_table($table_name, $db))
			{	
				// Создадим ее
				self::create_log_table($table_name, $db);
			}
		
			//После всех проверок Запишем лог
			$sql_log = self::create_log_string($table_name, $db, $log1, $log2, $log3);
			
			// Ошибки
			if(!$sql_log){
				self::$error[] = $db->errorInfo();
			}
		}
		
		// Выброс массива ошибок
		if($exception === true and !empty(self::$error)){
			throw new Exception("<pre>".print_r(self::$error, true)."</pre>");
		}	
	}

    /**
     * Запись лога в указанный файл
     * @param string $str Строка лога
     * @param string $link Обсалютная ссылка на файл лога (пример: /my_log.txt)
     * @param bool $time Добавить время и дату в строку
     * @param bool $ip Добавить IP адрес в строку
     * @param bool $file Добавить файл вызова этой функции
     * @param bool $user_agent Добавить USER_AGENT юзера
     * @return true or false
     */
	public static function my_log($str, $link, $user_agent=false, $time=true, $ip=true, $file=true)
	{
		$str_param = array(); // Параметры юзера
		// Если задан вывод времени
		if($time){
			$time = date("d-m-Y H:i:s");
			$str_param[]= $time;
		}
		// Если задан вывод IP адреса
		if($ip){
			$ip = $_SERVER["REMOTE_ADDR"];
			$str_param[]= $ip;
		}
		// USER_AGENT юзера
		if($user_agent){
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
			$str_param[]= $user_agent;
		}
		// Адрес файла и параметры
		if($file){
			$file = $_SERVER["REQUEST_URI"];
			$str_param[]= $file;
		}
		$str_param[] = $str;
		$link = $_SERVER["DOCUMENT_ROOT"].$link;
		$str = implode($str_param, " ** "); // Создаем лог строку
		$str = $str."\r\n"; // Добавляем переход строки
		if($handle = fopen($link, 'a')){
			fwrite($handle, $str);
			fclose($handle);
			return true;
		} else {
			return false;
		}
	
	}

    /**
     * Запись в файл, адрес по умолчанию /upload/damp_log.txt
     * ( константа LINK_LOG )
     * @param mixed $var
     * @param string $link - Абсолютный путь до файла без $_SERVER["DOCUMENT_ROOT"]
	 * @param bool $append - Если true файл НЕ будет перезаписан, последующий дамп будет добавлен в конец файла
     * @return number or false
	 * @throws Exception
     */
	public static function damp_to_file($var, $link=self::LINK_LOG, $append = false)
	{
		$link = $_SERVER["DOCUMENT_ROOT"].$link;
		
		if(is_array($var)){
			$var = var_export($var, true);
			$var = str_replace("  ", "\t", $var);
		}

		if($append === false)
			$bite_count = file_put_contents($link, $var, FILE_APPEND);
		else
			$bite_count = file_put_contents($link, $var);
		
		return $bite_count;
	}

    /**
     * Перекодировка строк и массивов из кодировки WINDOWS-1251 в кодировку UTF-8
     * Если исходная кодировка отличная от WINDOWS-1251, то использовать данный метод не надо.
     * Проверки на кодировку нет, так как она всегда глючит!
     * @param string or array
     * @return string or array or false
     */
	public static function convert_encoding($str_array){
	
		if(is_array($str_array)){
			// Перебор всех элементов массива рекурсивно
			foreach ($str_array as $key => &$value){
			   if(is_array($value))
					self::convert_encoding($value);
			   else
					$value = iconv("WINDOWS-1251", "UTF-8", $value);
			}
			return $str_array;
	
		}
	
		if (is_string($str_array)){
			// Перекодируем
			$str_array = iconv("WINDOWS-1251", "UTF-8", $str_array);
			return $str_array;
		}
		return false;
	}

    /**
     * Использование оперативной памяти
     * @param Вызывается без параметров
     * @return string or false
     */
	public static function memory_usage(){
		if( function_exists('memory_get_usage') ) {
	
			$mem_usage = memory_get_usage(true);
	
				if ($mem_usage < 1024){
					$memory_usage = round($mem_usage, 3)." bytes"; // Округление до 3-го знака
					return $memory_usage;
				}
				elseif ($mem_usage < 1048576) {
					$memory_usage = round($mem_usage/1024, 3);
					$memory_usage.= " Кб";
					return $memory_usage;
				}
				else {
					$memory_usage = round($mem_usage/1048576, 3);
					$memory_usage.= " Мб";
					return $memory_usage;
				}
		} else {
			return false;
		}
	}

	
	/**
	 * Рекурсивная смена прав на файлы и папки
	 * @param string $path - обсалютный путь до корневой папки (включая $_SERVER["DOCUMENT_ROOT"] !!!)
	 * @param int $filemode - права на файлы
	 * @param int $dirmode - права на папки
	 */
	public static function chmod_r($path, $filemode, $dirmode) {
		// Если директория
		if (is_dir($path) ) {
			if (!chmod($path, $dirmode)) {
				$dirmode_str=decoct($dirmode);
				self::$error[] = "<span style='color: #DD0000'>Не удается применить filemode '$dirmode_str' на директорию '$path'. Директория '$path' была пропущена, chmod не был изменен </span><br>\n";
				return;
			} else {
				self::$mess[] = "<span style='color: #009900'>chmod директории $path был изменен </span><br>\n";
			}
			$dh = opendir($path);
			while (($file = readdir($dh)) !== false) {
				if($file != '.' && $file != '..') {  // skip self and parent pointing directories
					$fullpath = $path.'/'.$file;
					self::chmod_r($fullpath, $filemode,$dirmode);
				}
			}
			closedir($dh);
		} else {
			// Если символьная ссылка
			if (is_link($path)) {
				self::$error[] = "<span style='color: #DD0000'>Символьная ссылка '$path' была пропущена </span><br>\n";
				return;
			}
			// Если файл
			if (!chmod($path, $filemode)) {
				$filemode_str=decoct($filemode);
				self::$error[] = "<span style='color: #DD0000'>Не удалось применить filemode '$filemode_str' для файла '$path' </span><br>\n";
				return;
			} else {
				self::$mess[] = "<span style='color: #009900'>chmod файла $path был изменен </span><br>\n";
			}
		}
	}

	/**
	 * Аналог штатной print_r()
	 * @param mixed $arParams - Массив или строка с параметрами
	 * @param bool $return - Возвращать ли значение, по умолчанию печатает
	 * @return string
	 */
	public static function print_r($arParams, $return = false)
	{
		$result = "<!-- debug start --><pre>\n\t".print_r($arParams, true)."\n\t</pre><!-- debug end -->";
		if(!$return)
			echo $result;
		else
			return $result;
	}

	/**
	 * Проверка наличия AJAX запроса
	 * @return bool
	 */
	public static function isAjax()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Функия для вывода ошибок на сайте. В качестве входных параметров может принимать любой тип данных
	 *
	 * @param mixed $message
	 * @param bool $title
	 * @param string $color
	 */
	public static function DebugMessage($message, $title = false, $color = "#008B8B")
	{
		echo '<table border="0" cellpadding="5" cellspacing="0" style="border:1px solid '.$color.';margin:2px;"><tr><td>';

		if (strlen($title)>0)
			echo '<p style="color: '.$color.';font-size:11px;font-family:Verdana;">['.$title.']</p>';

		if (is_array($message) || is_object($message))
		{
			echo '<pre style="color:'.$color.';font-size:11px;font-family:Verdana;">'; print_r($message); echo '</pre>';
		}
		else
			echo '<p style="color:'.$color.';font-size:11px;font-family:Verdana;">'.$message.'</p>';

		echo '</td></tr></table>';
	}

	/*                                         */
	/* Функции для работы только внутри класса */
	/*                                         */

	// Создание индексной таблицы
	private static function create_index_table($db){
		if(!is_object($db)) return false;
		$sql = "CREATE  TABLE index_table ('id' INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , 'table_name' VARCHAR UNIQUE, 'table_description' VARCHAR )";
		if(!$db->exec($sql)){
			self::$error[] = "Ошибка базы (метка 1): ".$db->errorInfo();
			return false;
		}
	}
	
	// Создание таблицы с логами
	private static function create_log_table($table_name, $db){
		if(!$table_name or !is_object($db)) return false;
		// Сначала заносим запись в индексную таблицу
		$sql = "INSERT INTO index_table (table_name, table_description) VALUES ($table_name, '')";
		if(!$db->exec($sql)){
			self::$error[] = "Ошибка базы (метка 1): ".$db->errorInfo();
			return false;
		}


		// И только потом создаем таблицу с логами
		$sql = "CREATE TABLE $table_name ('id' INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , 'log1' TEXT check(typeof('log1') = 'text'), 'log2' TEXT check(typeof('log2') = 'text'), 'log3' TEXT check(typeof('log3') = 'text'), 'date' INTEGER, 'ip' VARCHAR, 'file' VARCHAR, 'user_agent' VARCHAR)";
		if(!$db->exec($sql)){
			self::$error[] = "Ошибка базы (метка 2): ".$db->errorInfo();
			return false;
		}
	}
	
	// Проверка на существование таблицы с логами
	private static function check_log_table($table_name, $db){
		$sql = "SELECT id FROM index_table WHERE table_name = $table_name";
		$res = $db->query($sql);
		if(!$res){
			self::$error[] = "Ошибка базы (метка 3): ".$db->errorInfo();
			return false;
		}
		if($res->fetch()){
			return true;
		} else {
			return false;
		}
	}
	
	// Запись лога в таблицу лога
	private static function create_log_string($table_name, $db, $log1 = "", $log2 = "", $log3 = ""){
	
		// Строка лога
		$log1 = $db->quote($log1);  // Для безопасности данных
		$log2 = $db->quote($log2);
		$log3 = $db->quote($log3);
		// Время
		$time = time();
		$time = $db->quote($time);
		// IP адрес
		$ip = $_SERVER["REMOTE_ADDR"];
		$ip = $db->quote($ip);
		// USER_AGENT юзера
		$user_agent = $_SERVER["HTTP_USER_AGENT"];
		$user_agent = $db->quote($user_agent);
		// Адрес файла и параметры
		$file = $_SERVER["REQUEST_URI"];
		$file = $db->quote($file);
	
		$sql = "INSERT INTO $table_name (log1, log2, log3, date, ip, file, user_agent) VALUES ($log1, $log2, $log3, $time, $ip, $file, $user_agent)";
	
		if($db->exec($sql)){
			return true;
		} else {
			self::$error[] = "Ошибка базы (метка 4): ".$db->errorInfo();
			return false;
		}
	}


} // END Class
?>
