( function($) {

	var collapseItem = function(e) {
		e.preventDefault();
		
		var image = $("div", this);
		var originalClass = image.attr("class");
		var id = $(this).parent().attr('id');
		
		image.attr("class", "loadingMini");
		
		var row = $(this).parent().parent().parent();
		var images = $('.setRight', row);
		
		$.post("./application/lib/weblcms/courses_rights_editor/javascript/ajax/course_group_right_location.php", {
			rights : id,
			locations: locations
			}, function(result)
			{
				if (result)
				{
					var newClass = $.ajax({
							type: "POST",
							url: "./application/lib/weblcms/courses_rights_editor/javascript/ajax/course_group_right_location_class.php",
							data: { rights : id },
							async: false
						}).responseText;

					image.attr("class", newClass);
				}
				else
				{
					image.attr("class", originalClass);
					alert(getTranslation('Failure', 'rights'));
				}
			}
		);
	};

	function bindIcons() {
		$("a.setRight").unbind();
		$("a.setRight").bind('click', collapseItem);
	}

	$(document).ready( function() {
		bindIcons();
	});

})(jQuery);