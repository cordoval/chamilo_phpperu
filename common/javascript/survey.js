var timer;

function handle_timer()
{
	var value = $('#start_time').val();
	value = parseInt(value);
	value++;
	$('#start_time').val(value);
	
	var max_time = $('#max_time').val();
	max_time = parseInt(max_time);
	
	var text = max_time - value;
	
	if(max_time - value < 10)
		text = '<span style="color: red;">' + text + '</span>';
		
	$('.time').html(text);
	
	if(max_time == 0)
		return;
	
	if(value >= max_time)
	{
		alert(getTranslation('TimesUp', 'repository'));
		$(".process").click();
	}
	else
	{
		timer = setTimeout('handle_timer()', 1000);
	}
}
	
( function($) 
{
	$(document).ready( function() 
	{
		handle_timer();
	});
	
})(jQuery);