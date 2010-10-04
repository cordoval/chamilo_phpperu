$(function () 
{	
	function title_clicked(ui, evt)
	{
		var div = $(this).parent();
		$(".collapse", div).toggle();
	}
	
	function showall_clicked(ui, evt)
	{
		$(".collapse").css('display', 'block');
		return false;
	}
	
	function hideall_clicked(ui, evt)
	{
		$(".collapse").css('display', 'none');
		return false;
	}
		
	$(document).ready(function ()
	{
		//$(".process_title").toggle();
		$(".collapse").toggle();
		$(".title").live('click', title_clicked);
		$("#showall").live('click', showall_clicked);
		$("#hideall").live('click', hideall_clicked);
	});

});