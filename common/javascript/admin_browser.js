/*global $, document, jQuery, window */

$(function () 
{
	function tab_clicked(evt, ui)
	{
		var href = $('a', $(this)).attr('href');
		href = href.substr(7);
		
		var translation = getTranslation(href, 'admin');
		
		var link = $('a', $('li:last', $('#breadcrumbtrail')));
		var url = link.attr('href');
		
		link.text(translation);
		url = url.substr(0, url.indexOf('tab='));
		url = url + 'tab=' + href;
		link.attr('href', url);
	}
	
	$(document).ready(function ()
	{
		$('li', $('#admin_tabs')).live('click', tab_clicked);
	});	
});