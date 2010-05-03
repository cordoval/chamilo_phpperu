( function($)
{
    var handle_user = function()
    {
		var form = $(this).closest("form");
    	$("button[name=select_format]").click();
    }
    
    $(document).ready( function()
    {
		$("button[name=select_format]").hide();
    	$(".change_user").change(handle_user);
    });
})(jQuery);