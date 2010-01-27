$(function ()
{	
	$(document).ready(function ()
	{
		$('textarea.html_editor.RepositoryQuestion').ckeditor({
			toolbar : 'RepositoryQuestion'
		});
		$('textarea.html_editor.WikiPage').ckeditor({
			toolbar : 'WikiPage'
		});
//		$('textarea.html_editor').ckeditor({
//			
//		});
	});

});