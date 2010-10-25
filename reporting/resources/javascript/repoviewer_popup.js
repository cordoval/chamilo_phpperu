var selected_question_id = 0;

( function($) 
{
	function show_repo_viewer(ev, ui)
	{
		var path = get_path('WEB_PATH');
		var html = '<iframe style="border: none; width: 1000px; height: 550px; z-index: 3500;" border="0" src="' + path + 'core.php?application=repository&go=repo_viewer"></iframe>';
		$.modal(html, {
			overlayId: 'modalOverlay',
		  	containerId: 'repoViewerModalContainer',
		  	opacity: 75
		});
		
		selected_question_id = $(this).attr('id');
		
		return false;
	}
	
	$(document).ready( function() 
	{
		$(".select_file").toggle();
		$(".select_file_button").toggle();
		$(".select_file_text").toggle();
		$(".select_file_button").live('click', show_repo_viewer);
	});
	
	function get_path(path) 
	{		
		var path = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/path.php",
			data: { path: path },
			async: false
		}).responseText;
		
		return path;
	}
	
})(jQuery);

function object_selected(object)
{
	jQuery.modal.close();
	
	var title = jQuery.ajax({
		type: "POST",
		url: "./common/javascript/ajax/content_object.php",
		data: { object: object },
		async: false
	}).responseText;
	
	jQuery('.select_file_text[name="' + selected_question_id + '_2_text"]').attr('value', title);
	jQuery('#' + selected_question_id + '_2').attr('value', object);
}