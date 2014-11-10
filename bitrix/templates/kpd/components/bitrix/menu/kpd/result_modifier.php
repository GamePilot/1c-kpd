<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// Преобразование херового вывода меню в нормальный массив
$i=0;
foreach($arResult as &$value){
	if($value["DEPTH_LEVEL"] == 1){
		$value["SUB_MENU"] = array();
		$key = $i; // Индекс корневого массива
	} else {
		$arResult[$key]["SUB_MENU"][] = $value;
		unset($arResult[$i]);
	}
	$i++;
}
unset($value);