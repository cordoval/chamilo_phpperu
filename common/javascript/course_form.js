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
	
	$(document).ready(function ()
	{
		$('.viewablecheckbox').viewableStyle();
		$('.viewablecheckbox').setViewableStyle();
		$('.empty').live('click', reset);
		$('.course_type_selector').live('change',reload_form);
	});

});