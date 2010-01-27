$(function ()
{	
	$(document).ready(function ()
	{
	   $('textarea.html_editor').tinymce({
		      script_url : getPath('WEB_PATH') + 'plugin/html_editor/tinymce/tiny_mce.js',
		      theme : "advanced",
		      theme_advanced_toolbar_location : "top",
		      theme_advanced_toolbar_align : "left"
		   });
	});

});