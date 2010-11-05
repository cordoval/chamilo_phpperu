( function($)
{
	var toggleText = function(e, ui)
	{
		$("#textlist").toggle();
		$("#showtext").toggle();
		$("#hidetext").toggle();
	};
        var toggleImage = function(e, ui)
	{
		$("#imagelist").toggle();
		$("#showimage").toggle();
		$("#hideimage").toggle();
	};

	var toggleVideo = function(e, ui)
	{
		$("#videolist").toggle();
		$("#showvideo").toggle();
		$("#hidevideo").toggle();
	};

	function bindIcons()
	{
		$("#showtext").unbind();
		$("#showtext").bind('click', toggleText);
		$("#hidetext").unbind();
		$("#hidetext").bind('click', toggleText);

                $("#showimage").unbind();
		$("#showimage").bind('click', toggleImage);
		$("#hideimage").unbind();
		$("#hideimage").bind('click', toggleImage);

                $("#showvideo").unbind();
		$("#showvideo").bind('click', toggleVideo);
		$("#hidevideo").unbind();
		$("#hidevideo").bind('click', toggleVideo);

	}

	$(document).ready( function()
	{
		$("#textlist").toggle();
		$("#showtext").toggle();

                $("#imagelist").toggle();
		$("#showimage").toggle();

                $("#videolist").toggle();
		$("#showvideo").toggle();

		bindIcons();

	});

})(jQuery);