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
			case "form_send": // Получите в подарок новую книгу
				$this->form_send();
				break;
			case "receive_gift": // Получите в подарок
				$this->receive_gift();
				break;
			case "contact_form": // Оставьте заявку и мы свяжемся с Вами в ближайшее время
				$this->contact_form();
				break;
			case "online_call": // Модалка "Заказать звонок"
				$this->online_call();
				break;
			default:
				$this->error = "Не определен тип запроса";
				$this->send_ajax();
				return false;
		}


		return true;
	}


	/**
	 * Получите в подарок новую книгу
	 * @return bool
	 */
	public function form_send()
	{
		// Проверка полей
		$error = array();
		$arFieldsError = array(
			"name" => "",
			"phone" => "",
			"email" => ""
		);
		foreach($arFieldsError as $key => $value){
			if(empty($this->request[$key])){
				$error[] = $value;
				$this->error_keys[$key] = $arFieldsError[$key];
			}
		}		
		if(!empty($error)){
			$this->error = "Пожалуйста, заполните обязательные поля";
			$this->send_ajax();
			return false;
		}

		// Отправка на емаил
		$messID = CEvent::Send("FORM_KPD", "s1", $this->request, "N");
		if($messID > 0){
			$this->mess = "Ваша заявка отправлена";
			$this->send_ajax();
		} else {
			$this->error = "Заявку отправить не удалось";
			$this->send_ajax();
			return false;
		}
		
		
		return true;
	}

	/**
	 * Получите в подарок
	 * @return bool
	 */
	public function receive_gift()
	{
		// Проверка полей
		$error = array();
		$arFieldsError = array(
			"name" => "",
			"email" => ""
		);
		foreach($arFieldsError as $key => $value){
			if(empty($this->request[$key])){
				$error[] = $value;
				$this->error_keys[$key] = $arFieldsError[$key];
			}
		}
		if(!empty($error)){
			$this->error = "Пожалуйста, заполните обязательные поля";
			$this->send_ajax();
			return false;
		}

		// Отправка на емаил
		$messID = CEvent::Send("FORM_KPD_2", "s1", $this->request, "N");
		if($messID > 0){
			$this->mess = "Ваша заявка отправлена";
			$this->send_ajax();
		} else {
			$this->error = "Заявку отправить не удалось";
			$this->send_ajax();
			return false;
		}


		return true;
	}

	/**
	 * Оставьте заявку и мы свяжемся с Вами в ближайшее время
	 * @return bool
	 */
	public function contact_form()
	{
		// Проверка полей
		$error = array();
		$arFieldsError = array(
			"name" => "",
			"phone" => "",
			"email" => "",
			"text" => ""
		);
		foreach($arFieldsError as $key => $value){
			if(empty($this->request[$key])){
				$error[] = $value;
				$this->error_keys[$key] = $arFieldsError[$key];
			}
		}
		if(!empty($error)){
			$this->error = "Пожалуйста, заполните обязательные поля";
			$this->send_ajax();
			return false;
		}

		// Отправка на емаил
		$messID = CEvent::Send("SEND_CONTACT_FORM", "s1", $this->request, "N");
		if($messID > 0){
			$this->mess = "Ваша заявка отправлена";
			$this->send_ajax();
		} else {
			$this->error = "Заявку отправить не удалось";
			$this->send_ajax();
			return false;
		}


		return true;
	}

	/**
	 * Модалка "Заказать звонок"
	 * @return bool
	 */
	public function online_call()
	{
		// Проверка полей
		$error = array();
		$arFieldsError = array(
			"name" => "Введите свое имя",
			"phone" => "Неверный формат номера",
			"time_call" => ""
		);
		foreach($arFieldsError as $key => $value){
			if(empty($this->request[$key])){
				$error[] = $value;
				$this->error_keys[$key] = $arFieldsError[$key];
			}
		}
		// Проверка телефона
		$this->phone($this->request["phone"], "phone");
		
		if(!empty($error) or !empty($this->error_keys)){
			$this->error = "Пожалуйста, заполните обязательные поля";
			$this->send_ajax();
			return false;
		}

		// Отправка на емаил
		//$messID = CEvent::Send("ONLINE_CALL", "s1", $this->request, "N");
		$messID = 0;
		if($messID > 0){
			$this->mess = "Ваша заявка отправлена";
			$this->send_ajax();
		} else {
			$this->error = "Заявку отправить не удалось";
			$this->send_ajax();
			return false;
		}


		return true;
	}

	/**
	 * Отправка формы "Заказать On-line звонок"
	 * НЕ ИСПОЛЬЗУЕТСЯ!
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

	/**
	 * Валидация номера телефона
	 * @param string $str - Строка проверки
	 * @param string $name - Имя поля
	 * @return bool
	 */
	private function phone($str, $name){
		$pattern = '/^[0-9+( )-]{7,20}$/';
		if(!preg_match($pattern, $str)){
			$this->error_keys[$name] = "Неверный формат номера";
			return false;
		} else {
			return true;
		}
	}

}