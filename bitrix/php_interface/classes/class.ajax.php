<?
/**
 * Class ajax для ответов на ajax запросы
 */
class ajax
{
	
	/**
	 * Тип запроса
	 * contact - Отправка формы обратной связи
	 * @var string
	 */
	public $type = "";
	
	public $error = "";
	public $error_keys = "";
	public $mess = "";
	public $html = "";
	public $captcha_sid = "";
	public $captchaUrl = "";
	
	
	private $request = array();

	function __construct()
	{
		$this->request = $this->clear_data($_REQUEST);
		
		if(isset($this->request["type"]) and !empty($this->request["type"]))
			$this->type = $this->request["type"];
		else
			return false;
		
		switch ($this->type){
			case "contact_form":
				$this->send_contact_form();
				break;
			case "on_line_call_form":
				$this->send_call_form();
				break;
			default:
				$this->error = "Не определен тип запроса";
				$this->send_ajax();
				return false;
		}


		return true;
	}

	/** 
	 * Отправка формы обратной связи
	 * @return bool
	 */
	public function send_contact_form()
	{
		// Проверка полей
		$error = array();
		$arFieldsError = array(
			"fio" => "Не указано Имя",
			"email" => "Не указан E-mail",
			"text" => "Введите текст сообщения",
			"connect" => "Не указана предпочтительный способ связи"
		);
		foreach($arFieldsError as $key => $value){
			if(empty($this->request[$key])){
				$error[] = $value;
				$this->error_keys[$key] = $arFieldsError[$key];
			}
		}
		
		// Проверка капчи
		global $APPLICATION;
		if(!$APPLICATION->CaptchaCheckCode($this->request["captcha"], $this->request["captcha_sid"])){
			$error[] = "Не верно введен код капчи";
			$this->error_keys["captcha"] = "Не верно введен код капчи";
		}
		
		if(!empty($error)){
			$this->error = implode("<br>", $error);
			$this->captcha_sid = $APPLICATION->CaptchaGetCode();
			$this->captchaUrl = "/bitrix/tools/captcha.php?captcha_sid=".$this->captcha_sid;
			$this->send_ajax();
			return false;
		}
		
		// Отправка на емаил
		$messID = CEvent::Send("SEND_CONTACT_FORM", "s1", $this->request, "N");
		
		// Добавление в инфоблок
		CModule::IncludeModule('iblock');
		$el = new CIBlockElement;

		$PROP = array();
		$PROP["email"] = $this->request["email"];
		$PROP["tel"] = $this->request["tel"];
		$PROP["town"] = $this->request["town"];
		$PROP["connect"] = $this->request["connect"];

		$arLoadProductArray = Array(
			"MODIFIED_BY"    => 1,
			"IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
			"IBLOCK_ID"      => 1,
			"PROPERTY_VALUES"=> $PROP,
			"NAME"           => $this->request["fio"],
			"ACTIVE"         => "Y", // активен
			"PREVIEW_TEXT"   => $this->request["text"],
			"DETAIL_TEXT"    => ""
		);

		$el->Add($arLoadProductArray);		
		
		if($messID > 0){
			$this->mess = "Ваше сообщение успешно отправлено!";
			$this->send_ajax();
		} else {
			$this->error = "Сообщение отправить не удаось";
			$this->send_ajax();
			return false;
		}

		return true;
	}

	/**
	 * Отправка формы "Заказать On-line звонок"
	 * @return bool
	 */
	public function send_call_form()
	{
		// Проверка полей
		$error = array();
		$arFieldsError = array(
			"fio" => "Не указано Имя",
			"tel" => "Не указан телефон",
			"town" => "Не написан город",
			"connect" => "Вы не указали, когда удобнее звонить"
		);
		foreach($arFieldsError as $key => $value){
			if(empty($this->request[$key])){
				$error[] = $value;
				$this->error_keys[$key] = $arFieldsError[$key];
			}
		}
		
		// Проверка капчи
		global $APPLICATION;
		if(!$APPLICATION->CaptchaCheckCode($this->request["captcha"], $this->request["captcha_sid"])){
			$error[] = "Не верно введен код капчи";
			$this->error_keys["captcha"] = "Не верно введен код капчи";
		}

		if(!empty($error)){
			$this->error = implode("<br>", $error);
			$this->captcha_sid = $APPLICATION->CaptchaGetCode();
			$this->captchaUrl = "/bitrix/tools/captcha.php?captcha_sid=".$this->captcha_sid;
			$this->send_ajax();
			return false;
		}

		// Отправка на емаил
		$messID = CEvent::Send("SEND_ONLINE_CALL_FORM", "s1", $this->request, "N");

		// Добавление в инфоблок
		CModule::IncludeModule('iblock');
		$el = new CIBlockElement;

		$PROP = array();
		$PROP["tel"] = $this->request["tel"];
		$PROP["town"] = $this->request["town"];
		$PROP["connect"] = $this->request["connect"];

		$arLoadProductArray = Array(
			"MODIFIED_BY"    => 1,
			"IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
			"IBLOCK_ID"      => 2,
			"PROPERTY_VALUES"=> $PROP,
			"NAME"           => $this->request["fio"],
			"ACTIVE"         => "Y", // активен
			"PREVIEW_TEXT"   => "",
			"DETAIL_TEXT"    => ""
		);

		$el->Add($arLoadProductArray);

		if($messID > 0){
			$this->mess = "Ваше сообщение успешно отправлено!";
			$this->send_ajax();
		} else {
			$this->error = "Сообщение отправить не удаось";
			$this->send_ajax();
			return false;
		}

		return true;
	}


	/**
	 * Вывод аякса из параметров
	 */
	private function send_ajax()
	{
		$arJson = array();
		$arJson["mess"] = $this->mess;
		$arJson["error"] = $this->error;
		$arJson["html"] = $this->html;
		$arJson["captcha_sid"] = $this->captcha_sid;
		$arJson["captchaUrl"] = $this->captchaUrl;
		$arJson["error_keys"] = $this->error_keys;
		
		echo json_encode($arJson);
	}

	/**
	 * Очистка входных данных
	 * @param array $data - Данные для чистки
	 * @return array
	 */
	private function clear_data($data)
	{
		$result = array();
		foreach($data as $key => $value)
		{
			if(is_array($value)){
				$result[$key] = $this->clear_data($value);
				continue;
			}
			$key = strip_tags($key);
			$key = trim($key);
			$value = strip_tags($value);
			$value = trim($value);
			$result[$key] = $value;
		}
		
		return $result;
	}
}