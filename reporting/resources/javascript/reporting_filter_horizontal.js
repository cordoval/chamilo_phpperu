$(document).ready(function()
{	
	$(".reporting_filter_hide_container").toggle();
	
	$(".reporting_filter_text").bind("click", showBlockScreen);
	$(".reporting_filter_hide").bind("click", hideBlockScreen);
	
	function showBlockScreen()
	{
		var id = $(this).attr('id').replace('_reporting_filter_text', '');
		
		$(".reporting_filter_text").hide();
			$(".reporting_filter").slideToggle(300);
		
		return false;
	}
	
	function hideBlockScreen()
	{
		var id = $(this).attr('id').replace('_reporting_filter_hide', '');
		
		$(".reporting_filter").slideToggle(300, function()
		{
			$(".reporting_filter_text").show();
		});
		
		return false;
	}
});