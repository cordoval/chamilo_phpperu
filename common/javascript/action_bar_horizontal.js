$(document).ready(function()
{	
	$(".action_bar_hide_container").toggle();
	
	$(".action_bar_text").bind("click", showBlockScreen);
	$(".action_bar_hide").bind("click", hideBlockScreen);
	
	function showBlockScreen()
	{
		var id = $(this).attr('id').replace('_action_bar_text', '');
		
		$("#"+ id +"_action_bar_text").slideToggle(300, function()
		{
			$("#"+ id +"_action_bar").slideToggle(300);
		});
		
		return false;
	}
	
	function hideBlockScreen()
	{
		var id = $(this).attr('id').replace('_action_bar_hide', '');
		
		$("#"+ id +"_action_bar").slideToggle(300, function()
		{
			$("#"+ id +"_action_bar_text").slideToggle(300);
		});
		
		return false;
	}
});