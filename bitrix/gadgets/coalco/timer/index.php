<?php
$timer_file = $_SERVER["DOCUMENT_ROOT"]."/upload/timer.txt";
$day = 0;
$hour = 0;
$minutes = 0;
$iteration = "of";

if(isset($_POST["submit"]))
{
	if($_POST["day"] > 0)
		$day = $_POST["day"];
	if($_POST["hour"] > 0)
		$hour = $_POST["hour"];
	if($_POST["minutes"] > 0)
		$minutes = $_POST["minutes"];
	if(isset($_POST["iteration"]))
		$iteration = $_POST["iteration"];
	
	// Запись метки времени
	$timer = $day*24*60*60 + $hour*60*60 + $minutes*60;
	$text = $timer."|".time()."|".$iteration;
	if(file_put_contents($timer_file, $text) === false){
		echo "<div class='error'>Невозможно создать файл с меткой времени, вероятно проблема с правами на запись по адресу .$timer_file;</div>";
		return false;
	}	
} else {
	// Взять метку времени и заполнить <input>
	if(is_file($timer_file)){
		$text = file_get_contents($timer_file);
		$arTimer = explode("|", $text);
		$time = $arTimer[0] + $arTimer[1] - time();

		$arTimer_file = array();
		if($time > 0){
			$arTimer_file = get_hour_time($time);
		}
		// Если таймер зациклен
		elseif($arTimer[2] == "on" and $time < 0){
			$period_count = floor($time/$arTimer[0]) * (-1);
			$time = time() - $arTimer[1] - $arTimer[0]*$period_count;
			$arTimer_file = get_hour_time($time);
		} else {
			$arTimer_file = array(
				"day" => 0,
				"hour" => 0,
				"minutes" => 0
			);
		}
		
		$day = $arTimer_file["day"];
		$hour = $arTimer_file["hour"];
		$minutes = $arTimer_file["minutes"];
		$iteration = $arTimer[2];
		
	} else {
		if(file_put_contents($timer_file, "0|".time()."|of") === false){
			echo "<div class='error'>Невозможно создать файл с меткой времени, вероятно проблема с правами на запись по адресу .$timer_file;</div>";
			return false;
		}
	}
}

?>

<style type="text/css">
	#timer input {
		margin-right: 15px;
	}
</style>

<form action="" method="post" id="timer">
	<label for="day">Дни: </label>
	<input id="day" name="day" value="<?=$day?>">
	<label for="hour">Часы: </label>
	<input id="hour" name="hour" value="<?=$hour?>">
	<label for="minutes">Минуты: </label>
	<input id="minutes" name="minutes" value="<?=$minutes?>">

	<label for="iteration">Повторять циклом: </label>
	<input type="checkbox" id="iteration" name="iteration" <?if($iteration == "on") echo "checked"?>>
	
	<input type="submit" name="submit" value="Установить таймер">
</form>