<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('1С КПД');
?>

<!-- slider -->
<div id="slideshow">
	<div id="slidesContainer">
		<div class="slide sl1">
			<h2>1С:Документооборот</h2>
			<p>Эффективная система организации делопроизводства и управления процессами</p>
			<a href="#" class="button">Демоверсия</a>
			<a href="#" class="button">Просмотреть демонстрацию</a>
		</div>
		<div class="slide sl1">
			<h2>1С:Документооборот</h2>
			<p>Эффективная система организации делопроизводства и управления процессами</p>
			<a href="#" class="button">Демоверсия</a>
			<a href="#" class="button">Просмотреть демонстрацию</a>
		</div>
		<div class="slide sl1">
			<h2>1С:Документооборот</h2>
			<p>Эффективная система организации делопроизводства и управления процессами</p>
			<a href="#" class="button">Демоверсия</a>
			<a href="#" class="button">Просмотреть демонстрацию</a>
		</div>
	</div>
</div>

<!-- none -->
<div class="container boxes kl-fun">
	<h1>Ключевые функции «1С:Документооборот»</h1>
	<div class="box">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-delo.jpg" /><span>Делопроизводство</span></p>
		<p>Учет официальных документов организации в соответствии с российским законодательством, ГОСТами, отечественной и международной делопроизводственной практикой.</p>
	</div>
	<div class="box">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-vhod.jpg" /><span>Входящие и исходящие документы</span></p>
		<p>Регистрация, рассмотрение, ознакомление и исполнение входящих документов. Согласование, утверждение, регистрация и отправка исходящих документов.</p>
	</div>
	<div class="box">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-vnut.jpg" /><span>Внутренние документы</span></p>
		<p>Обработка любых типов внутренних документов: организационно-распорядительные, информационно-справочные, кадровые, бухгалтерские, личные, оперативные, стратегические.</p>
	</div>
	<div class="box no-mr">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-nomen.jpg" /><span>Номенклатура дел</span></p>
		<p>Ведение номенклатуры дел по годам, организациям, подразделениям, видам документов, категориям и срокам хранения.</p>
	</div>
	<div class="box">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-businnes.jpg" /><span>Бизнес-процессы</span></p>
		<p>Отправка документов возможна по простым маршрутам: рассмотрение, исполнение, согласование, утверждение, регистрация, ознакомление, поручение, или по сложному комплесному процессу</p>
	</div>
	<div class="box">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-inter.jpg" /><span>Интеграция</span></p>
		<p>Простой и бесшовный обмен данными с учетными решениями на платформе 1С:Предприятие: веб-сервисы, электронная почта, синхронизация данных, СВД (МЭДО).</p>
	</div>
	<div class="box">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-pravo.jpg" /><span>Права доступа</span></p>
		<p>Политики доступа, грифы доступа, делегирование прав, передача прав подчиненных руководителям, передача прав подчиненных руководителям, шифрование файлов.</p>
	</div>
	<div class="box no-mr">
		<p class="title"><img src="/bitrix/templates/kpd/images/icon-uprav.jpg" /><span>Управление договорами</span></p>
		<p>Автозаполнение файла, внутреннее и внешнее согласование, контроль подписания, планирование исполнения, отслеживание, продление, расторжение договора, дополнительные соглашения.</p>
	</div>
</div>
<hr>
<!-- none -->
<div class="container boxes form1">
	<div class="content">
		<p class="title">Получите в подарок новую книгу<br /> <span>«1С:Документооборот. 200 вопросов и ответов»</span> <br />или <span>просмотр записи мини-семинара</span><br /> по 1С:Документооборот для специалистов</p>
		<form action="/ajax/form_send.php" method="post" data-type="form_send" enctype="application/x-www-form-urlencoded">
			<div class="left">
				<input type="text" name="name" placeholder="Имя">
				<input type="text" name="phone" placeholder="Телефон">
				<input type="text" name="email" placeholder="E-mail">
			</div>
			<div class="right">
				<p>Удобный для Вас формат обучения:</p>
				<input value="Корпоративное обучение" type="radio" name="format" checked>Корпоративное обучение<br>
				<input value="Курсы в Учебном центре 1С-КПД" type="radio" name="format">Курсы в Учебном центре 1С-КПД<br />
				<a href="#" class="button">Оставить заявку</a>
			</div>
		</form>
	</div>
</div>
<hr>
<!-- none -->
<div class="container boxes form2">
	<div class="content">
		<p class="title">Получите в подарок</p>
		<p>«1C:Электронное обучение. Работа с прикладным решением «1C:Документооборот» (облако)</p>
		<form action="/ajax/form_send.php" method="post" data-type="receive_gift" enctype="application/x-www-form-urlencoded">
			<input type="text" name="name" placeholder="Имя">
			<input type="text" name="email" class="email" placeholder="E-mail">
			<a href="#" class="button">Оставить заявку</a>
		</form>
	</div>
</div>
<hr>
<!-- none -->
<div class="container boxes form3">
	<div class="content">
		<p>Оставьте заявку и мы свяжемся с Вами в ближайшее время</p>
		<form action="/ajax/form_send.php" method="post" data-type="contact_form" enctype="application/x-www-form-urlencoded">
			<input type="text" name="name" placeholder="Имя">
			<input type="text" name="phone" placeholder="Телефон">
			<input type="text" name="email" placeholder="E-mail">
			<select name="tarif">
				<option selected value="Тариф 1">Тариф 1</option>
				<option value="Тариф 2">Тариф 2</option>
				<option value="Тариф 3">Тариф 3</option>
			</select>
			<textarea name="text" placeholder="Комментарий"></textarea>
			<a href="#" class="button">Отправить заявку</a>
		</form>
	</div>
</div>


<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>