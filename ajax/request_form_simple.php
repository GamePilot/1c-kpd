<div class="modal w270">
	<!--<a href="#" class="close"><span>Close</span></a>-->
	<form action="/ajax/form_send.php" method="post" data-type="send_rate" enctype="application/x-www-form-urlencoded">
		<label>Ф.И.О <span>*</span></label>
		<input type="text" name="name">

		<label>Телефон <span>*</span></label>
		<input type="text" name="phone">

		<label>Электронная почта <span>*</span></label>
		<input type="text" name="email">
		
		<input type="hidden" name="rate" value="<?=$_GET["rate"]?>">
 
		<p class="info-b no-br"><span>*</span> - Поля, обязательные для заполнения</p>

		<a href="#" class="button modal">Отправить заявку</a>
	</form>
</div>