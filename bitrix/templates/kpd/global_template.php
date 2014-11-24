<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<?$APPLICATION->ShowHead();?>
	<title><?$APPLICATION->ShowTitle(false)?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" type="text/css" href="/bitrix/templates/kpd/css/style.css" />
	<link rel="stylesheet" type="text/css" href="/bitrix/templates/kpd/css/sweet-alert.css" />
	<link rel="stylesheet" type="text/css" href="/bitrix/templates/kpd/css/colorbox.css" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="/bitrix/templates/kpd/js/sweet-alert.js"></script>
	<script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> <!--плагин ajaxForm-->
	<script type="text/javascript" src="/bitrix/templates/kpd/js/jquery.colorbox-min.js"></script>

	<script type="text/javascript" src="/bitrix/templates/kpd/js/main.js"></script>
	<script type="text/javascript" src="/bitrix/templates/kpd/js/jquery.plugin.js"></script>
	<script type="text/javascript" src="/bitrix/templates/kpd/js/jquery.countdown.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			var currentPosition = 0;
			var slideWidth = 1000;
			var slides = $('.slide');
			var numberOfSlides = slides.length;

			// Remove scrollbar in JS
			$('#slidesContainer').css('overflow', 'hidden');

			// Wrap all .slides with #slideInner div
			slides
				.wrapAll('<div id="slideInner"></div>')
				// Float left to display horizontally, readjust .slides width
				.css({
					'float' : 'left',
					'width' : slideWidth
				});

			// Set #slideInner width equal to total width of all slides
			$('#slideInner').css('width', slideWidth * numberOfSlides);

			// Insert controls in the DOM
			$('#slideshow')
				.prepend('<span class="control" id="leftControl">Clicking moves left</span>')
				.append('<span class="control" id="rightControl">Clicking moves right</span>');

			// Hide left arrow control on first load
			manageControls(currentPosition);

			// Create event listeners for .controls clicks
			$('.control')
				.bind('click', function(){
					// Determine new position
					currentPosition = ($(this).attr('id')=='rightControl') ? currentPosition+1 : currentPosition-1;

					// Hide / show controls
					manageControls(currentPosition);
					// Move slideInner using margin-left
					$('#slideInner').animate({
						'marginLeft' : slideWidth*(-currentPosition)
					});
				});



			// manageControls: Hides and Shows controls depending on currentPosition
			function manageControls(position){
				// Hide left arrow if position is first slide
				if(position==0){ $('#leftControl').hide() } else{ $('#leftControl').show() }
				// Hide right arrow if position is last slide
				if(position==numberOfSlides-1){ $('#rightControl').hide() } else{ $('#rightControl').show() }
			}
		});
	</script>
</head>
<body>

<!-- шапка -->
<div id="panel">
	<?$APPLICATION->ShowPanel();?>
</div>

<div class="header">
	<div class="container">
		<a class="logo" alt="1С-КПД Корпоративные порталы и документооборот" title="1С-КПД Корпоративные порталы и документооборот" href="/"><span>1С-КПД Корпоративные порталы и документооборот</span></a>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "kpd", Array(
				"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
				"MENU_CACHE_TYPE" => "A",	// Тип кеширования
				"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
				"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
				"MENU_CACHE_GET_VARS" => array(	// Значимые переменные запроса
					0 => "",
				),
				"MAX_LEVEL" => "1",	// Уровень вложенности меню
				"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
				"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
				"DELAY" => "N",	// Откладывать выполнение шаблона меню
				"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
			),
			false
		);?>
		<div class="clr"></div>
	</div>
</div>





{Page Text}





<!-- подвал -->
<div class="container boxes footer">
	<img src="/bitrix/templates/kpd/images/logo-bottom.jpg">
	<div class="social">
		<a href="http://vk.com/1cdocflow" class="vk" target="_blank"><span>vk</span></a>
		<a href="https://www.facebook.com/groups/1cdocflow/" class="facebook" target="_blank"><span>facebook</span></a>
		<a href="https://twitter.com/1ckpd/" class="twitter" target="_blank"><span>twitter</span></a>
		<a href="http://www.youtube.com/user/1ckpd" class="skype" target="_blank"><span>linked</span></a>
	</div>
	<p>© 1C-КПД 2014</p>
</div>

<!-- Наверх -->
<div class="container">
	<div class="top-content">
		<a href="#"><img src="/bitrix/templates/kpd/images/vverh.png" alt="Наверх" title="Наверх" /></a>
	</div>
</div>
</body>
</html>