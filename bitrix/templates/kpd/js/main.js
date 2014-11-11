$(document).ready(function() {
	// Прокрутка страницы
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
	$("a.form").click(function(e){
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
					sweetAlert({
						title: "Спасибо!",
						text: ajax.mess,
						type: "success", 
						timer: 5000
					});
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

	// Отправка модалок
	$("a.modal").live("click",function(e){
		var ajax = {};
		var form = $(this).parents("form");
		var type = form.attr("data-type");
		form.find("input").attr("placeholder", "").removeClass("error"); // Удалить все ошибки
		form.ajaxSubmit({
			data: {
				type  : type
			},
			dataType: "json",
			success: function(data) {
				ajax = data;
				if(ajax.mess.length > 0)
				{
					// Успешная отправка
					// TODO
				}
				else if (ajax.error.length > 0)
				{
					// Сообщения об ошибках
					for (var input_name in ajax.error_keys){
						form.find("input[name=" + input_name + "]").attr("placeholder", ajax.error_keys[input_name]).addClass("error");
					}
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

	// Модальные окна
	$("a.ajax").click(function(e){
		var href = $(this).attr("href");
		show_modal(href);
		
		e.preventDefault();
	});
	
	function show_modal(href){
		$.colorbox({
			href:href
		});
	}
	

}); // end ready()