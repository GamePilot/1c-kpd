<?php
// Перенаправление после POST запроса
if(!empty($_POST["DEBUG"]))
    header("Location: {$_SERVER['REQUEST_URI']}");
/**
 * Смотрелка для файлов лога из SQLite
 * @copyright       Ilya Shafran
 * @author          Ilya Shafran
 * @version 1.01 (10 февраля 2014 года)
 */
class DebugViewer {

	/**
	 * Адрес скрипта jquery
	 */
	const JQUERY = "jquery-1.7.min.js";

	/** 
	 * Адрес скрипта datepicker
	 */
	const DATEPICKER = "datepicker-2.6.js";

	/**
	 * Плагин для inline редактирования текста прямо на странице
	 */
	const JEDITABLE = "jquery.jeditable.js";

	/** 
	 * JavaScript вьювера
	 */
	const MAIN_SCRIPT = "main.js";

	/**
	 * Стили CSS
	 */
	const STYLE = "style.css";

	/**
	 * Адрес файла для AJAX запросов
	 */
	const AJAX = "ajax.php";

	/**
	 * Адрес файла с логами базы SQLite с $_SERVER["DOCUMENT_ROOT"]
	 */
	public $LINK_LOG_DB;

	/**
	 * Адрес папки с видами
	 * @var string
	 */
	public $view;

	/**
	 * Массив SQL запросов
	 * @var array
	 */
	public $sql = array();

	/**
	 * Массив ошибок
	 * @var array
	 */
	public $error = array();

	/**
	 * Ресурс базы
	 * @var PDO
	 */
	private $db;

	/**
	 * Режим работы дебага
	 * @var bool
	 */
	private $debug = false;

    /**
     * Смотрелка для файлов лога из SQLite. Конструктор класса, создает объект базы
     * @param string $file - Обсалютный адрес файла без $_SERVER["DOCUMENT_ROOT"] с логами базы SQLite по умолчанию константа LINK_LOG_DB класса MyDebug
	 * @param bool $debug true - Включить режим дебага
	 * @throws Exception
     */
    function __construct($debug = true, $file = "")
	{
		if(empty($file)){
			$this->LINK_LOG_DB = $_SERVER["DOCUMENT_ROOT"].MyDebug::LINK_LOG_DB;
		} else {
			$this->LINK_LOG_DB = $_SERVER["DOCUMENT_ROOT"].$file;
		}
		
		if(!is_file($this->LINK_LOG_DB)){
			throw new Exception("Файл с базой данных логов по адресу $this->LINK_LOG_DB не найден");
		}
		
        $this->db = new PDO("sqlite:$this->LINK_LOG_DB");
		
		if(!is_object($this->db)){
			throw new Exception("Не удалось создать объект базы данных");
		}

		// Папка вида
		$this->view = str_replace($_SERVER["DOCUMENT_ROOT"], "", __DIR__."/views/");
		
		// Режим дебага
		$this->debug = $debug;
		
		session_start();
    }

	// Вывод ошибок для дебага, после завершения работы класса
	function __destruct()
	{
		if(!empty($this->error) and $this->debug === true){
			echo "<div style='border: 2px solid #B54515; clear: both; padding: 5px;'>
				<p>В ходе выполнения скрипта произошли ошибки:</p>
				<pre>".print_r($this->error, true)."</pre>
				<p>Список SQL апросов:</p>
				<pre>".print_r($this->sql, true)."</pre>
			</div>";
		}
	}

    /**
     * Отрисовка списка таблиц в базе
     * @returns string or false
     */
     public function createListTables()
	 {
         $arParams = array(); // Исходные данные
         $arResult = array(); // Результат
         $arParams = $this->selectTables();
         if(!$arParams)
            return false;
		 
		 $img = $this->view."icDelete.png";
         $arResult = "<div id='table_list'><strong>Таблицы логов:</strong><ul>\n";
         foreach($arParams as $items){
			 $count = $this->countStrTable($items["table_name"]);
             $arResult.= "
             \t<li>
             <a href='?name=$items[table_name]'>$items[table_name]($count)</a>
             (<span class='edit_table' id='$items[id]'>$items[table_description]</span>)
             <span class='del' data-id='$items[id]'><img src='$img' /></span>
             </li>";
         }
         $arResult.= "</ul></div>";
		 
         return $arResult;
     }


    /**
     * Отрисовка таблицы с логами
     * @param string $table_name - Имя таблицы
     * @param int $page - Страница таблицы, если записей много
     * @param int $limit - Лимит показа логов на 1 странице
     * @param string $order - Сортировка
     * @param boolean $user_agent - Показывать ли строку User Agent
     * @returns string or false
     */
    public function createTableLogs($table_name, $page = 1, $limit = 100,
                                    $order="date", $user_agent = false)
    {
        $table = ""; // Строка таблицы
        $pageNavigation = ""; // Строка навигации по страницам
        $table_name = strip_tags(trim($table_name)); // Обработка полученной строки имени
        // Если в $page приходит FALSE или строка
        if(!$page or (int) $page < 1)
            $page = 1;
        // Если в $page приходит FALSE
        if(!$order)
            $order = "date";
        $arResult = $this->selectLogs($table_name, $page, $limit, $order);
        if(!$arResult){
            return false;
        }

        // Создаем строку результата
        $table.= "<table>\n";
        $table.= "\t<tr>\n
        \t\t<th><a href='?name=$table_name&page=$page&order=id' title='Сортировка по колонке'>ID</a></th>\n
        \t\t<th><a href='?name=$table_name&page=$page&order=log1' title='Сортировка по колонке'>Строка лога 1</a></th>\n
        \t\t<th><a href='?name=$table_name&page=$page&order=log2' title='Сортировка по колонке'>Строка лога 2</a></th>\n
        \t\t<th><a href='?name=$table_name&page=$page&order=log3' title='Сортировка по колонке'>Строка лога 3</a></th>\n
        \t\t<th><a href='?name=$table_name&page=$page&order=date' title='Сортировка по колонке'>Дата события</a></th>\n
        \t\t<th><a href='?name=$table_name&page=$page&order=ip' title='Сортировка по колонке'>IP Юзера</a></th>\n
        \t\t<th><a href='?name=$table_name&page=$page&order=file' title='Сортировка по колонке'>Файл от куда вызвано</a></th>\n";

        if($user_agent)
            $table.= "\t\t<th><a href='?name=$table_name&page=$page&order=user_agent' title='Сортировка по колонке'>User Agent</a></th>\n";
        $table.= "\t</tr>\n";

        foreach ($arResult as $items){
            $table.="\t<tr>\n";
            $table.="\t\t<td>".$items['id']."</td>\n";
            $table.="\t\t<td>".$items['log1']."</td>\n";
			$table.="\t\t<td>".$items['log2']."</td>\n";
			$table.="\t\t<td>".$items['log3']."</td>\n";
            $table.="\t\t<td>".date("d-m-Y H:i:s", $items['date'])."</td>\n";
            $table.="\t\t<td>".$items['ip']."</td>\n";
            $table.="\t\t<td>".$items['file']."</td>\n";
            if($user_agent)
                $table.="\t\t<td>".$items['user_agent']."</td>\n";
            $table.="\t</tr>\n";
        }

        $table.= "</table>";

        // Отрисовка навигации
        if(isset($_SESSION["DATE_TO"]) and isset($_SESSION["DATE_FROM"])){
            // С фильтром
            $countStr = $this->countStrTableFilter($table_name);
        } else {
            // Без фильтра
            $countStr = $this->countStrTable($table_name);
        }
        if($countStr > $limit){
            $pageNavigation.= "\n<div id='page_nav'>\nСтраницы:";

            $i = 0;
            do {
                $i++;
                if($i == $page)
                    $selected = " class='selected'";
                else
                    $selected = "";
                $pageNavigation.= "<a$selected href='?name=$table_name&page=$i&order=$order'>$i</a>\n";
            }
            while(($countStr - $limit*$i) > 0);

            $pageNavigation.="</div>\n";
        }

        // Смена сортировки
        if(!empty($_POST["DEBUG"]["sortBy"])){
           $_SESSION["DEBUB_SORT"] =  strip_tags(trim($_POST["DEBUG"]["sortBy"]));
        }
        if($_SESSION["DEBUB_SORT"] == "DESC"){
            $orderBy = "ASC";
        } else {
            $orderBy = "DESC";
        }
        $sort = "
        <div id='sort'>Текущее направление сортировки: <strong>".$_SESSION['DEBUB_SORT']."</strong>
            <form action='' method='post' enctype='application/x-www-form-urlencoded'>
            <input type='submit' value='Поменять' />
            <input type='hidden' value='$orderBy' name='DEBUG[sortBy]' />
            </form>
        </div>\n";

        // Колличество записей с установленным фильтром
        $strCountFilter = "";
        if($countFilter = $this->countStrTableFilter($table_name)){
            $strCountFilter = "
            <div id='countFilter'>Колличество записей при установленном фильтре:
            <strong>$countFilter</strong></div>";
        }

        return "<div id='table_logs'>".$strCountFilter.$pageNavigation.$table.$pageNavigation.$sort."</div>";
    }

    /**
     * Выводит HTML форму для ввода диапазона дат
     * @returns string
     */
    public function getDateSpan()
	{
        $result = ""; // Результат работы функции
        // Текущие установки (если есть)
        if(!empty($_SESSION["DATE_TO"]) and !empty($_SESSION["DATE_FROM"])) {
            $day_from = date("d", $_SESSION["DATE_FROM"]);
            $month_from = date("m", $_SESSION["DATE_FROM"]);
            $year_from = date("Y", $_SESSION["DATE_FROM"]);
            $day_to = date("d", $_SESSION["DATE_TO"]);;
            $month_to = date("m", $_SESSION["DATE_TO"]);
            $year_to = date("Y", $_SESSION["DATE_TO"]);
        } else {
            $day_from = "Day";
            $month_from = "Month";
            $year_from = "Year";
            $day_to = "Day";
            $month_to = "Month";
            $year_to = "Year";
        }

        // Форма календаря
        $result.= "
    <div id='getDateSpan'> <strong>Фильтр по датам</strong><br>
      <form action='' method='post' enctype='application/x-www-form-urlencoded'>
          <strong>От:</strong><br>
        <span id='select1'>
        <select name='DEBUG[day_from]'>
          <option>$day_from</option>
        </select>
        <select name='DEBUG[month_from]'>
          <option>$month_from</option>
        </select>
        <select id='sel1' name='DEBUG[year_from]'>
          <option>$year_from</option>
        </select>
        </span><br>
        <strong>До:</strong><br>
        <span id='select2'>
        <select name='DEBUG[day_to]'>
          <option>$day_to</option>
        </select>
        <select name='DEBUG[month_to]'>
          <option>$month_to</option>
        </select>
        <select id='sel2' name='DEBUG[year_to]'>
          <option>$year_to</option>
        </select>
        </span>
        <br>
        <br>
        <input type='submit' value='Применить фильтр'>
      </form>\n";
        $result.= "</div>\n";

        // JavaScript для работы календаря
        $result.= "
    <script type='text/javascript'>
        var breakPoint = true;

        // Если не подключена библиотека jQuery, выводим сообщение
        if(!window.jQuery){
            document.write('<br>Библиотека jQuery не подключена!<br>Фильтр по дате не работает!');
            breakPoint = false;
        }

        if(breakPoint){
            // Объекты календаря
            var dp1 = new DatePicker('#sel1');
            dp1.onDateChange = onDateChangeFunction;
            var dp2 = new DatePicker('#sel2');
            dp2.onDateChange = onDateChangeFunction;
        }

        // Данная функция вызывается по событию .onDateChange(дата выбрана) объектов dp1 и dp2
        // Забивает option нужными данными
        function onDateChangeFunction(){
            var selects1 = $('#getDateSpan span#select1 select option');
            selects1.eq(0).text(dp1.d);
            selects1.eq(1).text(dp1.m);
            selects1.eq(2).text(dp1.y);

            var selects2 = $('#getDateSpan span#select2 select option');
            selects2.eq(0).text(dp2.d);
            selects2.eq(1).text(dp2.m);
            selects2.eq(2).text(dp2.y);
        }
    </script>\n";

        return $result;
    }

    /**
     * Стили CSS Debug Viewer по умолчанию
     * @returns string
     */
    public function getDefaultStyle()
	{
		$href = $this->view.self::STYLE;
        return "<link type='text/css' rel='stylesheet' href='$href'>";
    }

	/**
	 * Возвращает javascript-ы вида
	 * @return string
	 */
	public function getDefaultScript()
	{
		// Подгружаемые скрипты
		$scripts = array(self::JQUERY, self::DATEPICKER, self::JEDITABLE, self::MAIN_SCRIPT);
		$result = "";
		foreach($scripts as $value){
			$src = $this->view.$value;
			$result.= "<script type='text/javascript' src='$src'></script>\r\n";
		}
		return $result;
	}

    /**
     * Возвращает кнопку для очистки сессии
     * @returns string
     */
    public function clearSession()
	{
        $result = ""; // Результат работы функции
        $result.="<div id='clearSession'><form action='' method='post' enctype='application/x-www-form-urlencoded'>\n
        <input name='DEBUG[clearSession]' type='submit' value='Очистить сессию' />\n
        </form></div>";

        // Очистка сессии
        if(isset($_POST["DEBUG"]["clearSession"])){
            unset($_SESSION["DEBUB_SORT"]);
            unset($_SESSION["DATE_FROM"]);
            unset($_SESSION["DATE_TO"]);
        }

        return $result;
    }

	/**
	 * Установка описания для таблицы логов
	 * @param $id - ID таблицы
	 * @param $mess - Описание
	 * @return string
	 */
	public function setTableDescription($id, $mess)
	{
		if((int) $id <= 0){
			return "Ошибка";
		}
		
		$sql = "UPDATE index_table SET table_description='$mess' WHERE id='$id'";
		$this->sql[] = $sql;
		$res = $this->db->exec($sql);
		if($res){
			return $mess;
		} else {
			$this->error[] = $this->db->errorInfo();
			return "Ошибка";
		}
	}

	/**
	 * Удаление таблицы с логами
	 * @param $id
	 * @return bool|string
	 */
	public function delTable($id)
	{
		if((int) $id <= 0){
			return false;
		}
		
		$sql = "SELECT id, table_name FROM index_table WHERE id='$id'";
		$this->sql[] = $sql;

		$res = $this->db->query($sql);
		if(!$res){
			$this->error[] = $this->db->errorInfo();
			return false;
		}

		$arResult = $res->fetch();
		$table_name = $arResult["table_name"];

		// Удаление таблицы логов
		$table_name = $this->db->quote($table_name);
		$sql = "DROP TABLE $table_name";
		$this->sql[] = $sql;
		$res->closeCursor(); // Закрыть предыдущее соединение, дабы избежать залочивания таблицы
		$this->db->exec($sql);
		
		// Удаление записи в индексной таблице
		$sql = "DELETE FROM index_table WHERE id='$id'";
		$this->sql[] = $sql;
		$this->db->exec($sql);

		return true;
	}
	

	/*                                         */
	/* Функции для работы только внутри класса */
	/*                                         */
	
	// Запрос данных из таблицы с логами
    private function selectLogs($table_name, $page=1, $limit = 100, $order="date")
	{
        $result = array(); // Строка результата
        $orderBy = ""; // Направление сортировки
        $break = true; // Точка остановки

        // Если нет имени таблицы
        if(!$table_name)
            return false;


        // Сортировка
        // Задание сортировки по умолчанию DESC
        if(!isset($_SESSION["DEBUB_SORT"]) or ($_SESSION["DEBUB_SORT"] != "ASC"
                                    and $_SESSION["DEBUB_SORT"] != "DESC"))
        {
            $_SESSION["DEBUB_SORT"] = "DESC";
        }
        $orderBy = $_SESSION["DEBUB_SORT"];

        // Ограниченная выборка по дате
        // Подготовка данных с датами
        $day_from = "";
        $month_from = "";
        $year_from = "";
        $day_to = "";
        $month_to = "";
        $year_to = "";
        $timestamp_from = false; // Unix метка времени
        $timestamp_to = false; // Unix метка времени
        if(!empty($_POST["DEBUG"]["day_from"]))
            $day_from = (int) strip_tags(trim($_POST["DEBUG"]["day_from"]));
        if(!empty($_POST["DEBUG"]["month_from"]))
            $month_from = (int) strip_tags(trim($_POST["DEBUG"]["month_from"]));
        if(!empty($_POST["DEBUG"]["year_from"]))
            $year_from = (int) strip_tags(trim($_POST["DEBUG"]["year_from"]));
        if(!empty($_POST["DEBUG"]["day_to"]))
            $day_to = (int) strip_tags(trim($_POST["DEBUG"]["day_to"]));
        if(!empty($_POST["DEBUG"]["month_to"]))
            $month_to = (int) strip_tags(trim($_POST["DEBUG"]["month_to"]));
        if(!empty($_POST["DEBUG"]["year_to"]))
            $year_to = (int) strip_tags(trim($_POST["DEBUG"]["year_to"]));

        // Подготовка данных с метками времени
        if($day_from and $month_from and $year_from and $day_to and $month_to and $year_to){
            $timestamp_from = mktime(0,0,0,$month_from,$day_from,$year_from);
            $timestamp_to = mktime(0,0,0,$month_to,$day_to,$year_to);
        } elseif(isset($_SESSION["DATE_TO"]) and isset($_SESSION["DATE_FROM"])) {
            $timestamp_from = $_SESSION["DATE_FROM"];
            $timestamp_to = $_SESSION["DATE_TO"];
        }

        // Определение лимита выборки
        $limit_max = $page*$limit;
        $limit_min = ($page-1)*$limit;

        // Подготовка и отправка запроса в базу данных
        $table_name = $this->db->quote($table_name);
        if($timestamp_from and $timestamp_to){
            // Если метки времени не FALSE подготавливаем запрос с их участием
            $sql = "SELECT * FROM $table_name
            WHERE date > $timestamp_from AND date < $timestamp_to
            ORDER BY $order $orderBy LIMIT $limit_min, $limit_max";
            $_SESSION["DATE_FROM"] = $timestamp_from;
            $_SESSION["DATE_TO"] = $timestamp_to;
        } else {
            $sql = "SELECT * FROM $table_name ORDER BY $order $orderBy LIMIT $limit_min, $limit_max";
        }
        $this->sql[] = $sql;
        $res = $this->db->query($sql);

        if(!$res) {
            $this->error[] = $this->db->errorInfo();
            return false;
        }
        else {
            $result = $res->fetchAll();
            return $result;
        }
    }

// Функция возвращает кол-во строк в таблице $table_name БЕЗ УЧЕТА фильтра
    private function countStrTable($table_name)
	{
        $arResult = 1;
        $sql = "SELECT COUNT() FROM '$table_name'";
        $this->sql[] = $sql;
        // Проверка есть ли $table_name
        if(!$table_name)
            return false;
        $res = $this->db->query($sql);

        if(!$res) {
			$this->error[] = $this->db->errorInfo();
            return false;
        }
        else {
            $arResult = $res->fetch();
            return $arResult["COUNT()"];
        }
    }

// Функция возвращает кол-во строк в таблице $table_name С УЧЕТОМ фильтра
    private function countStrTableFilter($table_name)
	{
        $arResult = 1;
        if(!isset($_SESSION["DATE_TO"]) or !isset($_SESSION["DATE_FROM"])){
            return "";
        } else {
            $timestamp_from = $_SESSION["DATE_FROM"];
            $timestamp_to = $_SESSION["DATE_TO"];
        }
        $sql = "SELECT COUNT() FROM '$table_name' WHERE date > $timestamp_from AND date < $timestamp_to";
        $this->sql[] = $sql;
        // Проверка есть ли $table_name
        if(!$table_name)
            return false;
        $res = $this->db->query($sql);

        if(!$res) {
			$this->error[] = $this->db->errorInfo();
            return false;
        }
        else {
            $arResult = $res->fetch();
            return $arResult["COUNT()"];
        }
    }

	/**
	 * Запрос данных имен всех таблиц, возвращает массив с результатом
	 * @return array|bool
	 */
	private function selectTables()
	{
        $sql = "SELECT id, table_name, table_description FROM index_table ORDER BY table_name ASC";
        $this->sql[] = $sql;
        $res = $this->db->query($sql);

        if(!$res) {
			$this->error[] = $this->db->errorInfo();
            return false;
        }
        else {
            $arResult = $res->fetchAll();
            return $arResult;
        }
    }


} // END Class
?>
