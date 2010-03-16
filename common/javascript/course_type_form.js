$(function () 
{
	var prev_text = 0;
	
	function change_max_number_enable() 
	{
		un = document.getElementById("unlimited");
		el = document.getElementById("max_number");
					
		if(un.checked)
		{
			el.disabled = true;
			prev_text = el.value;
			el.value = 0;
		}
		else
		{
			el.disabled = false;
			if(prev_text != 0)
				el.value = prev_text;
		}
	}
	
	function reset(evt,ui)
	{
		setTimeout(
			function()
			{
				$('.iphone').setiphoneCourseType();
				$('.viewablecheckbox').setviewableStyle();
				change_max_number_enable();
			},30);	
	}
	
	$(document).ready(function ()
	{
//		$('.iphone').iphoneCourseType();
//		$('.iphone').setiphoneCourseType();
//		$('.viewablecheckbox').viewableStyle();
//		$('.viewablecheckbox').setviewableStyle();
//		$('.empty').live('click', reset);
//		$('#unlimited').live('click', change_max_number_enable);
//		change_max_number_enable();
	});

});