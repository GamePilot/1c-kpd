<?php

// Автолоад классов
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/classes/AutoLoader.php");

// Автогенерация шаблона Битрикс из одного файла
$template = new BitrixTemplate("kpd");

if(!empty($template->error)){
	header('Content-type: text/html; charset=UTF-8');
	echo "<p>Критическая ошибка!</p>";
	MyDebug::print_r($template->error);
	die;
}

/**
 * Получение дней, часов и минут из секунд
 * @param $time (int) - Время в секундах
 * @return array
 */
function get_hour_time($time)
{
	$arRes = array(
		"day" => 0,
		"hour" => 0,
		"minutes" => 0
	);

	$arRes["day"] = floor($time/(24*60*60));
	if($arRes["day"] > 0)
		$time = $time - $arRes["day"]*24*60*60;
	$arRes["hour"] = floor($time/(60*60));
	if($arRes["hour"] > 0)
		$time = $time - $arRes["hour"]*60*60;
	$arRes["minutes"] = floor($time/60);
	
	return $arRes;	
}
