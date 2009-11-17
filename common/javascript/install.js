/*global $, document, jQuery, window */

$(function () {

	$(document).ready(function ()
	{
		$("#tabs ul").css('display', 'block');
		$("#tabs h2").hide();
		$("#tabs").tabs();
		$('#tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
        $(':checkbox').iphoneStyle({ checkedLabel: 'On', uncheckedLabel: 'Off'});
	});

});