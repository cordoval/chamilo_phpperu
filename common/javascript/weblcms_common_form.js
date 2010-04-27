$(function () 
{
	function disable_checkbox(elem)
	{    	
    	if (!elem.is(':checkbox'))
    		return;
    	elem.attr('checked', false);
    	elem.attr('disabled', 'disabled');
	}
	
	function enable_checkbox(elem)
	{    	
    	if (!elem.is(':checkbox'))
    		return;
    	elem.removeAttr('disabled');
	}
	
	function toggle_others(elem)
	{
		var code_elem = $(".code");
		var request_elem = $(".request");
		var value = -1;
		if(elem.length == 2)
			value = elem.siblings('input:checked').val();
		else
			value = elem.val();
		
		var id = -1;
		if(elem.length == 2)
			id = elem.siblings('input').attr('name');
		else
			id = elem.attr('id');
		
		if(value==0)
		{
			switch(id)
			{
				case 'direct_target_groups_option': 
					if(!$(".direct").is(':checked'))
						return false;
					disable_checkbox(request_elem);
					$('#requestBlock').css('display', 'none');
				case 'request_target_groups_option':
					if(elem.siblings('input:checked').attr('id') == 'receiver_request_target_groups' && !$(".request").is(':checked'))
						return false;
					disable_checkbox(code_elem);
					$('#codeBlock').css('display', 'none');
					break;
				case 'creation_groups_option': 
					if(!$(".creation").is(':checked'))
						return false;
					disable_checkbox($(".creation_on_request"));
					$('#creation_on_requestBlock').css('display', 'none');
					break;
			}
		}
		else
		{
			switch(id)
			{
				case 'direct_target_groups_option': 
				case 'request_target_groups_option':
					if($("input[name=code_fixed]").length == 0 
							&& ($('input[name=request_target_groups_option]:checked').attr('id').toString() != 'receiver_request_target_groups' 
							|| !$(".request").is(':checked')))
						enable_checkbox(code_elem);
					if($("input[name=request_fixed]").length == 0)
						enable_checkbox(request_elem);
					break;
				case 'creation_groups_option':
					enable_checkbox($(".creation_on_request"));
					break;
			}
		}
	}
	
	function change_block(elem)
	{
		var type = elem.attr('class').split(' ').slice(-1);
			block = $('#' + type + 'Block');

		if(elem.attr('checked'))
		{
			block.css('display', 'block');
			switch(type.toString())
			{
				case 'direct': 
				case 'request':
					toggle_others($('input[name=' + type + '_target_groups_option]'));
					break;
				case 'creation':
					toggle_others($('input[name=' + type + '_groups_option]'));
					break;
			}
		}
		else
		{
			block.css('display', 'none');
			switch(type.toString())
			{
				case 'direct': 
				case 'request':
					if($("input[name=code_fixed]").length == 0 
							&& ($('input[name=request_target_groups_option]:checked').attr('id').toString() != 'receiver_request_target_groups'
							|| !$(".request").is(':checked')))
						enable_checkbox($(".code"));
					if($("input[name=request_fixed]").length == 0)
						enable_checkbox($(".request"));
					break;
				case 'creation':
						enable_checkbox($(".creation_on_request"));
					break;
			}
		}
	}
	
	$.fn.init_everybody = function ()
	{
		var elem = $(this);
		
		elem.click(function()
		{
			toggle_others(elem);
		});

		toggle_others(elem);
	}
	
	$.fn.init_available_checkbox = function ()
	{
		return this.each(function()
			{
				var elem = $(this);
				
				elem.click(function()
					{
						change_block(elem);
					});
				
				change_block(elem);
			});
	}
	
	$(document).ready(function ()
	{
		$('.available').init_available_checkbox();
		$("input[name=direct_target_groups_option]").init_everybody();
		$("input[name=request_target_groups_option]").init_everybody();
	});
});