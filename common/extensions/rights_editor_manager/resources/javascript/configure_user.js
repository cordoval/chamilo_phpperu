( function($) {

	var collapseItem = function(e) {
		e.preventDefault();
		
		var image = $("div", this);
		var originalClass = image.attr("class");
		var id = $(this).parent().attr('id');
		
		var row = $(this).parent().parent().parent();
		var images = $('.setRight', row);
		
		image.attr("class", "loadingMini");

		$.post("./application/common/rights_editor_manager/javascript/ajax/user_right_location.php", {
			rights : id,
			locations: locations
			}, function(result)
			{
				if (result)
				{
					if(application == 'repository' && getPlatformSetting('use_cumulative_rights', 'repository') == 1)
					{
						$.each(images, function() 
						{
							var newClass = $.ajax({
								type: "POST",
								url: "./application/common/rights_editor_manager/javascript/ajax/user_right_location_class.php",
								data: { rights : $(this).parent().attr('id') },
								async: false
							}).responseText;
							
							var image = $("div", this);
							image.attr("class", newClass);
						});
					}
					else
					{
						var newClass = $.ajax({
							type: "POST",
							url: "./application/common/rights_editor_manager/javascript/ajax/user_right_location_class.php",
							data: { rights : id },
							async: false
						}).responseText;

						image.attr("class", newClass);
					}
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