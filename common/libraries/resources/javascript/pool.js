( function($) 
{
	var start_time_changed = function(ev, ui) 
	{	
		$(".start_time").each(retrieve_start_date);	
		
		stop_date.setFullYear(start_date.getFullYear());
		stop_date.setMonth(start_date.getMonth());
		stop_date.setDate(start_date.getDate());
		stop_date.setMinutes(start_date.getMinutes());
		stop_date.setHours(start_date.getHours() + 1);
		
		$(".stop_time").each(set_stop_date);

		//alert("start: " + start_date + " stop: " + stop_date); 
	}
	
	$(document).ready( function() 
	{
		$(".start_time").bind("change", start_time_changed);
	});
	
})(jQuery);