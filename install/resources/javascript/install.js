/*global $, document, jQuery, window */

$(function () {

	function selectall_clicked(evt, ui)
	{
		$('.application_check').attr('checked', true);
		$('.handle').css('left', '36px');
		$('.bg').css('left', '34px');
		$('.on').css('opacity', '1');
		$('.off').css('opacity', '0');
		return false;
	}
	
	function unselectall_clicked(evt, ui)
	{
		$('.application_check').attr('checked', false);
		$('.handle').css('left', '0px');
		$('.bg').css('left', '0px');
		$('.on').css('opacity', '0');
		$('.off').css('opacity', '1');
		return false;
	}
	
	$(document).ready(function ()
	{
		$("#tabs ul").css('display', 'block');
		$("#tabs h2").hide();
		$("#tabs").tabs();
		$('#tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
        $(':checkbox').iphoneStyle({ checkedLabel: 'On', uncheckedLabel: 'Off'});
        $('#selectbuttons').show();
        $('#selectall').live('click', selectall_clicked);
        $('#unselectall').live('click', unselectall_clicked);
	});

});