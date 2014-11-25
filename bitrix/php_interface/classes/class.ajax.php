<?
/**
 * Class ajax для ответов на ajax запросы
 */
class ajax
{
	
	/**
	 * Тип(код) запроса
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
				$this->form_send(array(
					"name" => "",
					"phone" => "",
					"email" => ""
				));
				break;
			case "receive_gift": // Получите в подарок
				$this->receive_gift(array(
					"name" => "",
					"email" => ""
				));
				break;
			case "contact_form": // Оставьте заявку и мы свяжемся с Вами в ближайшее время
				$this->contact_form(array(
					"name" => "",
					"phone" => "",
					"email" => "",
					"text" => ""
				));
				break;
			case "online_call": // Модалка "Заказать звонок"
				$this->online_call(array(
					"name" => "Введите свое имя",
					"phone" => "Неверный формат номера",
					"time_call" => ""
				));
				break;
			case "calculator": // Вычисления калькулятора
				$this->calculator(array(
					"user_count" => "Количество пользователей",
					"pay_period" => "Период оплаты"
				));
				break;
			case "send_rate": // Сопровождение тариф %name%
				$this->send_rate(array(
					"name" => "Введите свое имя",
					"phone" => "Неверный формат номера",
					"email" => "Неверный формат E-mail"
				));
				break;
			case "get_timer": // Получить секунды для таймера
				$this->get_timer();
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
	 * @param array $arFieldsError - массив обязательных полей
	 * @return bool
	 */
	public function form_send($arFieldsError)
	{
		// Проверка полей		
		$this->email($this->request["email"], "email"); // Проверка E-mail
		if(!$this->field_check($arFieldsError))
			return false;


		// Отправка на емаил
		$messID = CEvent::Send("FORM_KPD", "s1", $this->request, "N");
		if($messID > 0){
			$this->mess = "Ваша заявка отправлена. Окно закроется через 3 секунды!";
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
	 * @param array $arFieldsError - массив обязательных полей
	 * @return bool
	 */
	public function receive_gift($arFieldsError)
	{
		// Проверка полей		
		$this->email($this->request["email"], "email"); // Проверка E-mail
		if(!$this->field_check($arFieldsError))
			return false;

		// Отправка на емаил
		$messID = CEvent::Send("FORM_KPD_2", "s1", $this->request, "N");
		if($messID > 0){
			$this->mess = "Ваша заявка отправлена. Окно закроется через 3 секунды!";
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
	 * @param array $arFieldsError - массив обязательных полей
	 * @return bool
	 */
	public function contact_form($arFieldsError)
	{
		// Проверка полей		
		$this->email($this->request["email"], "email"); // Проверка E-mail
		if(!$this->field_check($arFieldsError))
			return false;

		// Отправка на емаил
		$messID = CEvent::Send("SEND_CONTACT_FORM", "s1", $this->request, "N");
		if($messID > 0){
			$this->mess = "Ваша заявка отправлена. Окно закроется через 3 секунды!";
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
	 * @param array $arFieldsError - массив обязательных полей
	 * @return bool
	 */
	public function online_call($arFieldsError)
	{
		// Проверка полей		
		$this->phone($this->request["phone"], "phone"); // Проверка телефона
		if(!$this->field_check($arFieldsError))
			return false;		

		// Отправка на емаил
		$messID = CEvent::Send("ONLINE_CALL_FORM", "s1", $this->request, "N");
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
	 * Вычисление цены для калькулятора
	 * @param $arFieldsError - массив обязательных полей
	 * @return bool
	 */
	public function calculator($arFieldsError)
	{
		$default_price = 1500;
		// Проверка полей
		// Пользователи от 1 до 40
		if($this->request["user_count"] < 1 or $this->request["user_count"] > 40)
			$this->request["user_count"] = "";
		// Месяцы от 1 до 12
		if($this->request["pay_period"] < 1 or $this->request["pay_period"] > 12)
			$this->request["pay_period"] = "";

		if(!$this->field_check($arFieldsError))
			return false;
		
		// Скидка за юзеров
		$n = $this->request["user_count"];
		$price = 0;
		$discount_price = $default_price;
		for($i = 1; $i <= $n; $i++)
		{
			if($i == 1){
				$price = $default_price;
				continue;
			}
			if($i > 1 and $i <= 10 ){
				$discount_price = $discount_price * 0.9;
				$price = $price + $discount_price;
				continue;
			}
			if($i > 10 and $i <= 20){
				$discount_price = $discount_price * 0.95;
				$price = $price + $discount_price;
				continue;
			}
			if($i > 20 and $i <= 40){
				$price = $price + $discount_price;
			}
		}
		
		// Скидка на срок оплаты
		$month = $this->request["pay_period"];
		$discount = 1;
		if($month == 1) $discount = 1;
		if($month >= 3) $discount = 0.95;
		if($month >= 6) $discount = 0.9;
		if($month >= 9) $discount = 0.85;
		if($month >= 12) $discount = 0.8;

		$price = round($price * $discount, 0);
		
		$this->mess = "Ok";
		$this->send_ajax(array(
			"month_price" => number_format(intval($price), 0, ' ', ' '),
			"price" => number_format(intval($price * $month), 0, ' ', ' ')
		));
		
		return true;		
	}

	/**
	 * Модалка "Сопровождение тариф %name%"
	 * @param array $arFieldsError - массив обязательных полей
	 * @return bool
	 */
	public function send_rate($arFieldsError)
	{
		// Проверка полей		
		$this->phone($this->request["phone"], "phone"); // Проверка телефона
		$this->email($this->request["email"], "email"); // Проверка email
		if(!$this->field_check($arFieldsError))
			return false;

		// Отправка на емаил
		$messID = CEvent::Send("SEND_RATE", "s1", $this->request, "N");
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
	 * Получить секунды для таймера
	 * @return bool
	 */
	public function get_timer()
	{		
		$timer_file = $_SERVER["DOCUMENT_ROOT"]."/upload/timer.txt";
		$text = file_get_contents($timer_file);
		$arTimer = explode("|", $text);
		$time = $arTimer[0] + $arTimer[1] - time();

		if($time > 0){
			$this->send_ajax(array("timer" => $time));
			return true;
		}
		// Если таймер зациклен
		elseif($arTimer[2] == "on" and $time < 0){
			$period_count = floor($time/$arTimer[0]) * (-1);
			$time = time() - $arTimer[1] - $arTimer[0]*$period_count;
			$this->send_ajax(array("timer" => $time));
			return true;
		} else {
			$this->send_ajax(array("timer" => 0));
			return true;
		}
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
	 * @param array $params - Доп. параметры
	 */
	private function send_ajax($params = array())
	{
		$arJson = array();
		$arJson["mess"] = $this->mess;
		$arJson["error"] = $this->error;
		$arJson["error_keys"] = $this->error_keys;
		if(!empty($this->html)) $arJson["html"] = $this->html;
		if(!empty($this->captcha_sid)) $arJson["captcha_sid"] = $this->captcha_sid;
		if(!empty($this->captchaUrl)) $arJson["captchaUrl"] = $this->captchaUrl;
		
		// Вывод доп. параметров
		if(!empty($params)){
			foreach ($params as $key => $value){
				$arJson[$key] = $value;
			}
		}
		
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

	/**
	 * Валидация Email
	 * @param string $str - Строка проверки
	 * @param string $name - Имя поля, если нужен целевой вывод
	 * @return bool
	 */
	private function email($str, $name){
		$pattern = "/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i";
		if(!preg_match($pattern, $str)){
			$this->error_keys[$name] = "Неверно введен E-mail";
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Проверка полей
	 * @param $arFieldsError - массив обязательных полей
	 * @return bool
	 */
	private function field_check($arFieldsError)
	{
		// Проверка полей
		$error = array();
		foreach($arFieldsError as $key => $value){
			if(empty($this->request[$key])){
				$error[] = $value;
				$this->error_keys[$key] = $arFieldsError[$key];
			}
		}

		if(!empty($error) or !empty($this->error_keys)){
			$this->error = "Пожалуйста, заполните обязательные поля";
			$this->send_ajax();
			return false;
		}
		
		return true;
	}

}