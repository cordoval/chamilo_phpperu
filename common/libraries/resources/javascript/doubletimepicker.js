var start_time_changed = function(ev, ui) 
{	
	$(".start_date").each(retrieve_start_date);	

	stop_date.setFullYear(start_date.getFullYear());
	stop_date.setMonth(start_date.getMonth());
	stop_date.setDate(start_date.getDate());
	stop_date.setMinutes(start_date.getMinutes());
	stop_date.setHours(start_date.getHours() + 1);

	$(".end_date").each(set_stop_date);

	//alert("start: " + start_date + " stop: " + stop_date); 
}

$(document).ready( function() 
{
	$(".start_date").live("change", start_time_changed);
});
	