<div class="modal">
	<!--<a href="#" class="close"><span>Close</span></a>-->
	<p class="title">Заказать звонок</p>
	<p>Оставьте заявку и мы свяжемся с Вами в указаное время.<br />Или позвоните нам сами: <strong>+7 (495) 640-00-93</strong></p>
	<form action="/ajax/form_send.php" method="post" data-type="online_call" enctype="application/x-www-form-urlencoded">
		<label>Представтесь, пожалуйста <span>*</span></label>
		<input type="text" name="name">
		<p class="info">Имеет смысл указать фамилию, если она потребуеться секретарю</p>

		<label>Контактный телефон <span>*</span></label>
		<input type="text" name="phone">
		<p class="info">Не забудьте код города и Ваш добавочный</p>

		<label>Удобное время звонка <span>*</span></label>
		<input type="text" name="time_call">
		<p class="info">Например, «Сегодня с 12:00 до 13:00» или Прямо сейчас!</p>

		<p class="info-b"><span>*</span> - Поля, обязательны к заполнению</p>

		<a href="#" class="button modal">Отправить</a>
	</form>
</div>