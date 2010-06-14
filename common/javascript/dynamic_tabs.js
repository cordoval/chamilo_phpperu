$(function() {

	function setSearchTab(e, ui)
	{
		var searchForm = $("div.action_bar div.search_form form");
		alert('haha');
		alert(searchForm.attr('action'));
	}
	
	$(document).ready(function() {
		$("a").live('tabsselect', setSearchTab);
	});

});