$(function () 
{
	var timer;
	
	function check_for_existing_names(e, ui)
	{
		var title = $("#title").attr("value");

		$.post("./repository/ajax/title_exists.php", {title: title}, function (data) 
		{
			if(data)
			{
				$('#message').html(data);
			}
			else
			{
				$('#message').html('');
			}
		});
	}
	
	$(document).ready(function () 
	{
		$("#title").keypress( function() {
			clearTimeout(timer);
			timer = setTimeout(check_for_existing_names, 750);
		});
	});

})