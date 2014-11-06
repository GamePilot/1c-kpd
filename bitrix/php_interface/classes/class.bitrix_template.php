<?
/**
 * Class BitrixTemplate
 * Автогенерация шаблонов для Битрикс
 */
class BitrixTemplate
{
	/**
	 * Путь до папки с шаблонами сайта
	 */
	const TemplateFolder = "/bitrix/templates/";

	/**
	 * Имя файла для синхронизации кэширования в шаблоне сайта
	 */
	const sync_file = "sync.ini";

	/**
	 * Метка вставки страницы для глобального шаблона
	 */
	const marker = "{Page Text}";

	/**
	 * Ошибки
	 * @var array
	 */
	public $error = array();

	/**
	 * Массив данных о времени синхронизации (Смотрит время изменения файла)
	 * @var array
	 */
	public $arSync = array();
	
	// Пути к файлам
	private $header = "";
	private $footer = "";
	private $global = "";
	private $sync_file = "";

	/**
	 * Генерация шаблонов Битрикс
	 * @param string $template - Имя шаблона
	 */
	function __construct($template)
	{
		if(strlen($template) < 1){
			$this->error[] = "Неверно переданно имя шаблона";
			return false;
		}			
		
		$this->header = $_SERVER["DOCUMENT_ROOT"].self::TemplateFolder.$template."/header.php";
		$this->footer = $_SERVER["DOCUMENT_ROOT"].self::TemplateFolder.$template."/footer.php";
		$this->global = $_SERVER["DOCUMENT_ROOT"].self::TemplateFolder.$template."/global_template.php";
		$this->sync_file = $_SERVER["DOCUMENT_ROOT"].self::TemplateFolder.$template."/".self::sync_file;
		
		if(!is_file($this->header) or !is_file($this->footer) or !is_file($this->global)){
			$this->error[] = "Не существует одного из файлов header.php footer.php или global_template.php";
			return false;
		}

		$this->setArSync();

		if(!is_file($this->sync_file)){
			$file = file_put_contents($this->sync_file, " ");
			if(!$file){
				$this->error[] = "Не удалось создать ini файл синхронизации";
				return false;
			}
			
			// Первая запись о синхронизации
			file_put_contents($this->sync_file, serialize($this->arSync));
		}
		
		$arSync = unserialize(file_get_contents($this->sync_file));
		$bitrix_template = true;

		// Генерация глобального шаблона
		if($arSync["header"] != $this->arSync["header"] or $arSync["footer"] != $this->arSync["footer"])
		{
			$header_text = file_get_contents($this->header);
			$footer_text = file_get_contents($this->footer);
			$global_text = $header_text."\n\n".self::marker."\n\n".$footer_text;

			file_put_contents($this->global, $global_text);
			
			// Обновление синхронизации
			$this->setArSync();
			file_put_contents($this->sync_file, serialize($this->arSync));
			$bitrix_template = false; // Предотвращение двойной синхронизации
		}
		
		// Генерация шаблонов Битрикс
		if($arSync["global"] != $this->arSync["global"] and $bitrix_template)
		{
			$global_text = file_get_contents($this->global);
			
			$arTemplates = explode(self::marker, $global_text);
			file_put_contents($this->header, $arTemplates[0]);
			file_put_contents($this->footer, $arTemplates[1]);

			// Обновление синхронизации
			$this->setArSync();
			file_put_contents($this->sync_file, serialize($this->arSync));
		}
	}


	/**
	 * Setter для arSync
	 */
	private function setArSync()
	{
		$this->arSync["header"] = filemtime($this->header);
		$this->arSync["footer"] = filemtime($this->footer);
		$this->arSync["global"] = filemtime($this->global);
	}
}