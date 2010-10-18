( function($)
{
    var handle_evaluation_format = function()
    {
		var form = $(this).closest("form");
    	$("button[name=select_format]").click();
    }
    
    $(document).ready( function()
    {
		$("button[name=select_format]").hide();
    	$(".change_evaluation_format").change(handle_evaluation_format);
    });
})(jQuery);