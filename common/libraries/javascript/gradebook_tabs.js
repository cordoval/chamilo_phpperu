/*global $, document, jQuery, window */

$(function () 
{
	$(document).ready(function ()
	{
//		$("#gradebook_tabs ul").css('display', 'block');
		var tabs = $("#gradebook_tabs").tabs();
		tabs.tabs('select', tabnumber);
	});
});