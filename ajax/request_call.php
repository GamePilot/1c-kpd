<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<div class="modal">
	<!--<a href="#" class="close"><span>Close</span></a>-->
	<p class="title">Заказать звонок</p>
	<p>Оставьте заявку и мы свяжемся с Вами в указаное время.<br />Или позвоните нам сами: <strong><?php Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("top_phone"); ?>
    +7 <?if($_SESSION['PHONE']){echo $_SESSION['PHONE'];}else{?>(495) 640-00-93<?}?>
    <?php Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("top_phone", "(495) 640-00-93"); ?></strong></p>
	<form action="/ajax/form_send.php" method="post" data-type="online_call" enctype="application/x-www-form-urlencoded">
		<label>Представьтесь, пожалуйста <span>*</span></label>
		<input type="text" name="name">
		<p class="info">Имеет смысл указать фамилию, если она потребуется секретарю</p>

		<label>Контактный телефон <span>*</span></label>
		<input type="text" name="phone">
		<p class="info">Не забудьте код города и Ваш добавочный</p>

		<label>Удобное время звонка <span>*</span></label>
		<input type="text" name="time_call">
		<p class="info">Например, «Сегодня с 12:00 до 13:00» или Прямо сейчас!</p>

		<p class="info-b"><span>*</span> - Поля, обязательные для заполнения</p>

		<a href="#" class="button modal">Отправить</a>
	</form>
</div>