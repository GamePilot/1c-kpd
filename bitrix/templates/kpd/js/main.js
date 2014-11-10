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

	// Отправка формы
	$("#submit").click(function(e){
		var ajax = {};
		var form = $(this).parents("form");
		var type = form.attr("data-type");
		form.ajaxSubmit({
			data: {
				type  : type
			},
			dataType: "json",
			success: function(data) {
				ajax = data;
				if(ajax.mess.length > 0)
				{
					sweetAlert("Спасибо!", ajax.mess, "success");
				} 
				else if (ajax.error.length > 0)
				{
					// Сообщения об ошибках
					sweetAlert("Извините", ajax.error, "error");
				}
			},
			error: function(){
				ajax.error = "Запрос выполнен неудачно";
			},
			complete: function(){
				//location.reload();
			}
		});

		e.preventDefault();
	});


}); // end ready()