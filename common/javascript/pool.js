( function($) 
{
	var start_date = new Date();
	var stop_date = new Date();
		
	var retrieve_start_date = function()
	{
		var name = $(this).attr("name");
		var value = $(this).attr("value");
		var type = name.substring(11, 12);
		
		parse_type_to_date(type, start_date, value);
	}
	
	var set_stop_date = function()
	{
		var name = $(this).attr("name");
		var value = $(this).attr("value");
		var type = name.substring(10, 11);
		
		parse_date_to_type(type, stop_date, $(this));
	}
	
	var parse_type_to_date = function(type, date_object, value)
	{
		if(type == 'd')
		{
			date_object.setDate(value);
			return;	
		}
		
		if(type == 'F')
		{
			date_object.setMonth(value - 1);
			return;	
		}
		
		if(type == 'Y')
		{
			date_object.setFullYear(value);
			return;	
		}
		
		if(type == 'H')
		{
			date_object.setHours(value);
			return;	
		}
		
		if(type == 'i')
		{
			date_object.setMinutes(value);
			return;	
		}
		
	}
	
	var parse_date_to_type = function(type, date_object, object)
	{
		if(type == 'd')
		{
			object.attr("value",date_object.getDate());
			return;	
		}
		
		if(type == 'F')
		{
			object.attr("value",date_object.getMonth() + 1);
			return;	
		}
		
		if(type == 'Y')
		{
			object.attr("value",date_object.getFullYear());
			return;	
		}
		
		if(type == 'H')
		{
			object.attr("value",date_object.getHours());
			return;	
		}
		
		if(type == 'i')
		{
			object.attr("value",date_object.getMinutes());
			return;	
		}
		
	}
	
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