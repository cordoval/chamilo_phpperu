jQuery(document).ready(function($) 
{
	disable_enter_key();
});

function disable_enter_key()
{
	textboxes = $("#lom_metadata input:text");
	if ($.browser.mozilla) 
	{
		$(textboxes).keypress(checkForEnter);
	} 
	else 
	{
		$(textboxes).keydown(checkForEnter);
	}
}

function checkForEnter(event) 
{
	if (event.keyCode == 13)
	{
		event.preventDefault();
	}
}
