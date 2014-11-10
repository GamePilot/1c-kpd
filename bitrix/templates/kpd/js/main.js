$(document).ready(function() {
	var delay = 1000; // Задержка прокрутки
	var top_show = 150; // В каком положении полосы прокрутки начинать показ кнопки "Наверх"
	$(window).scroll(function () {
		/* В зависимости от положения полосы прокрукти и значения top_show, скрываем или открываем кнопку "Наверх" */
		if ($(this).scrollTop() > top_show){
			$('div.top-content').show();
		} else {
			$('div.top-content').hide();
		}
	});
	$('div.top-content').click(function () {
		$('body, html').animate({
			scrollTop: 0
		}, delay);
	});
	
	// Меню
	$("ul.menu a").click(function(){
		var id;
		var position;
		id = $(this).attr("href").replace("#", "");
		position = $("div#" + id).position();
		if(position !== undefined){
			$('body, html').animate({
				scrollTop: position.top
			}, delay);
		} else {
			return false;
		}
	});



}); // end ready()