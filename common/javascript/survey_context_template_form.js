$(function () 
{
	
	function add_category(evt)
	{
		if(evt.keyCode==13)
		{
			evt.preventDefault();
			var add_elem = jQuery(document.createElement('input'));
			add_elem.attr('type', 'hidden');
			add_elem.attr('name', 'add[]');
			add_elem.val('1');
			$('#configuration_form').prepend(add_elem);
			$('#configuration_form').submit();
			return false;
		}
	}
	
	$(document).ready(function ()
	{
		$('#configuration_form').live('keypress', add_category);
	});

});