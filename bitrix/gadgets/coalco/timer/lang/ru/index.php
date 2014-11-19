<?
define(BAD,'<br><span style="color:#C00; font-weight: bold;">');
define(GOOD,'<br><span style="color:#090; font-weight: bold;">');
define(SPAN,'</span>');

$MESS ['PROG_FILE_UPLOAD_ERROR'] = BAD."Ошибка при загрузке файла. Неверный тип файла или превышен максимальный размер файла!".SPAN;
$MESS ['PROG_FILE_UPLOADED'] = GOOD."Загрузка файла прошла успешно!".SPAN;
$MESS ['PROG_NOT_FOUND_FILE'] = BAD."Ошибка чтения файла ".SPAN;
$MESS ['PROG_ELEMENT_ADD_ERROR'] = BAD."Ошибка добавления элемента. Код ошибки: ".SPAN;
$MESS ['PROG_ELEMENT_ADD'] = GOOD."Успешно загружен в базу элемент: ".SPAN;
$MESS ['NO_IBLOCK_ID'] = BAD."Не указан ID инфоблока с программой! Укажите его в настроках гаджета.".SPAN;
?>