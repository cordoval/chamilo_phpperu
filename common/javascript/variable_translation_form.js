$(function () {
	
	function toggle_clicked(evt, ui)
	{
		$('#show').toggle();
		$('#othertranslations').toggle();
		$('#hide').toggle();
		return false;
	}
	
	$(document).ready(function ()
	{
		$("#translation").focus();
		
		$('#show').toggle();
		$('#othertranslations').toggle();
		
        $('#show').live('click', toggle_clicked);
        $('#hide').live('click', toggle_clicked);
	});
	
});(jQuery)