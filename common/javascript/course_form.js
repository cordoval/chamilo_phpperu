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
	
	function reload_form(evt,ui)
	{
		var course_type_id = $(this).val(),
			course_id = $('.course_id').val();
			course_param = '';
			go = 'coursecreator';
			
		if(course_id!='')
		{
			course_param = '&course='+course_id+'&tool=course_settings';
			go = 'courseviewer';
		}
		
		window.location.replace("run.php?go="+go+"&course_type="+course_type_id+course_param+"&application=weblcms");
		
	    /*$.post("./application/lib/weblcms/ajax/alter_course_form.php", 
	    {
	    	course_type_id : course_type_id,
	    	course_id : course_id
	   	},  function(data) 
	   		{
	       		$('#form_container').empty();
	       		$('#form_container').append(data);
	       	}
	    );*/
	}
	
	function change_block(elem)
	{
		var block_name = elem.attr('class').split(' ').slice(-1) + 'Block';
			block = $('#' + block_name);

		if(elem.attr('checked'))
			block.css('display', 'block');
		else
			block.css('display', 'none');
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
		$('.viewablecheckbox').viewableStyle();
		$('.viewablecheckbox').setViewableStyle();
		$('.empty').live('click', reset);
		$('.course_type_selector').live('change',reload_form);
		$('.available').init_available_checkbox();
	});

});