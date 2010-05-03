/*global $, document, jQuery, window */

$(function () 
{
	$(document).ready(function ()
	{
		$("#gradebook_tabs ul").css('display', 'block');
		$("#gradebook_tabs").tabs();
		$("#my-evaluated-publications ul").css('display', 'block');
		$("#my-evaluated-publications").tabs();
		$("#my-evaluations ul").css('display', 'block');
		$("#my-evaluations").tabs();
	});
});