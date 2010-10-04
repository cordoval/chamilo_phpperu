jQuery(document).ready(function($)
{
	$('div.application').css('font-size', '0px');
	$('div.application').css('min-width', '30px');
	$('div.application').css('padding-bottom', '0px');
	$('div.application').css('margin-top', '12px');
	$('div.application').css('margin-bottom', '22px');
	
	$('div.application').mouseover(function()
	{
		$(this).css('font-size', '11px');
		$(this).css('min-width', '60px');
		$(this).css('padding-bottom', '10px');
		$(this).css('margin-top', '0px');
		$(this).css('margin-bottom', '10px');
	});
	
	$('div.application').mouseout(function()
	{
		$(this).css('font-size', '0px');
		$(this).css('min-width', '30px');
		$(this).css('margin-top', '12px');
		$(this).css('margin-bottom', '22px');
		$(this).css('margin-left', '0px');
		$(this).css('margin-right', '10px');
		$(this).css('padding-bottom', '0px');
	});
})