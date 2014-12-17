<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>
<?
$uri_indication['yandex'] = "utm_source=yandex_direkt";
$uri_indication['yandex2'] = "utm_source=yandex";
$uri_indication['google'] = "utm_source=google_adwords";
$uri_indication['google2'] = "utm_source=google";
$uri_indication['yclid'] = 'yclid';
$uri_indication['gclid'] = 'gclid';
$uri_indication['youtube'] = "utm_source=youtube";

if(strpos($_SERVER['REQUEST_URI'], $uri_indication['yandex']) || strpos($_SERVER['REQUEST_URI'], $uri_indication['yclid']) || strpos($_SERVER['REQUEST_URI'], $uri_indication['yandex2'])){
  $source_type = "yad";
  $_SESSION['PHONE'] = '(499) <strong>638-34-55</strong>';
}elseif(strpos($_SERVER['REQUEST_URI'], $uri_indication['google']) || strpos($_SERVER['REQUEST_URI'], $uri_indication['gclid']) || strpos($_SERVER['REQUEST_URI'], $uri_indication['google2'])) {
  $source_type = "gad";
  $_SESSION['PHONE'] = '(499) <strong>638-34-05</strong>';
}elseif(strpos($_SERVER['HTTP_REFERER'], 'yandsearch')){
  $source_type = "yas";
  $_SESSION['PHONE'] = '(499) <strong>638-34-03</strong>';
}elseif(strpos($_SERVER['HTTP_REFERER'], 'www.google.')){
  $source_type = "gs";
  $_SESSION['PHONE'] = '(499) <strong>638-34-89</strong>';
}elseif(strpos($_SERVER['HTTP_REFERER'], 'facebook')){
  $source_type = "fb";
  $_SESSION['PHONE'] = '(499) <strong>638-34-01</strong>';
}elseif(strpos($_SERVER['REQUEST_URI'], $uri_indication['youtube']) || strpos($_SERVER['HTTP_REFERER'], 'youtube')){
  $_SESSION['PHONE'] = '(499) <strong>638-34-02</strong>';
}else{
 
  if( $_SESSION['PHONE']=='(499) <strong>638-34-55</strong>' || $_SESSION['PHONE']=='(499) <strong>638-34-05</strong>' || $_SESSION['PHONE']=='(499) <strong>638-34-03</strong>' || $_SESSION['PHONE']=='(499) <strong>638-34-89</strong>' || $_SESSION['PHONE']=='(499) <strong>638-34-01</strong>' || $_SESSION['PHONE']=='(499) <strong>638-34-02</strong>'){
      
  }else{
    $_SESSION['PHONE'] = '(495) <strong>640-00-93</strong>';
  }
  
}
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

<!-- Yandex.Metrika informer -->
<a href="https://metrika.yandex.ru/stat/?id=27423332&amp;from=informer"
   target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/27423332/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
									   style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:27423332,lang:'ru'});return false}catch(e){}"/></a>
<!-- /Yandex.Metrika informer -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
	(function (d, w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter27423332 = new Ya.Metrika({id:27423332,
					clickmap:true,
					trackLinks:true,
					accurateTrackBounce:true});
			} catch(e) { }
		});

		var n = d.getElementsByTagName("script")[0],
			s = d.createElement("script"),
			f = function () { n.parentNode.insertBefore(s, n); };
		s.type = "text/javascript";
		s.async = true;
		s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

		if (w.opera == "[object Opera]") {
			d.addEventListener("DOMContentLoaded", f, false);
		} else { f(); }
	})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/27423332" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!--Google analytics-->
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-57494022-1', 'auto');
	ga('send', 'pageview');

</script>

</body>
</html>