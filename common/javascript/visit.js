/**
 * @author Michael Kyndt
 */
( function($)
{
	$(window).bind("beforeunload", function(e)
	{
        var response = $.ajax({
			type: "POST",
			url: "./user/ajax/leave.php",
			data: { tracker: tracker},
			async: false
		}).responseText;

        //alert(response);
        //alert('bla');
		//$(".charttype").bind('change',handle_charttype);
	});
})(jQuery);