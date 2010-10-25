/*global $, document, jQuery, window */

$(function () {

	function selectall_languages_clicked(evt, ui)
	{
		$('.language').attr('checked', true);
		return false;
	}
	
	function unselectall_languages_clicked(evt, ui)
	{
		$('.language').attr('checked', false);
		return false;
	}
	
	function lptype_clicked(evt, ui)
	{
		var type = $(this).attr('name');

		$('.lp_' + type).attr('checked', $(this).attr('checked'));
	} 
	
	$(document).ready(function ()
	{
        $('#selectall_languages').live('click', selectall_languages_clicked);
        $('#unselectall_languages').live('click', unselectall_languages_clicked);
        
        $('.lptype').live('click', lptype_clicked);
	});

});