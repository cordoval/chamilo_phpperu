
(function ($)
{
    var show = translation('Show', 'weblcms');
    var hide = translation('Hide', 'weblcms')
	$(document).ready(function () 
    {
        $("#showhide").click(function()
        {
            $(this).text($(this).text() == '['+hide+']' ? '['+show+']' : '['+hide+']');
            $("#content").toggle();return false;
        
        });
	});

    function translation(string, application) {
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;

		return translated_string;
	}

})(jQuery);

   



