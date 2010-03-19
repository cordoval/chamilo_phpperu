$(function () 
{
	
	function reset(evt,ui)
	{
		setTimeout(
			function()
			{
				$('.viewablecheckbox').setViewableStyle();
			},30);	
	}
	
	$(document).ready(function ()
	{
		$('.viewablecheckbox').viewableStyle();
		$('.viewablecheckbox').setViewableStyle();
		$('.empty').live('click', reset);
	});

});