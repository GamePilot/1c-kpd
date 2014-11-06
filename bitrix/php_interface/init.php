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
