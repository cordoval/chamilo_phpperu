(function ($) {
	
	var maxBlockHeight = 0, maxComplexBlockHeight = 0, checkboxes;
	
	function checkCompareCheckboxes(e, ui) {
		checkboxCount = $("table.data_table > tbody > tr > td > input.repository_version_browser_table_id:checkbox:checked").size();
		
		if (checkboxCount >= 2)
		{
			$("table.data_table > tbody > tr > td > input.repository_version_browser_table_id:checkbox:not(:checked)").attr('disabled', true);
		}
		else
		{
			$("table.data_table > tbody > tr > td > input.repository_version_browser_table_id:checkbox").removeAttr('disabled');
		}
	}
	
	$(document).ready(function () {
		
		$("div.create_block").each(function (i) {
			if ($(this).height() > maxBlockHeight)
			{
				maxBlockHeight = $(this).height();
			}
		});
		
		$("div.create_block").height(maxBlockHeight);
		$("#other_content_object_types").hide();
		
		$(".search_query").jSuggest({
			url: getPath('WEB_PATH') + 'repository/ajax/search_complete.php',
			type: "POST",
			loadingText: getTranslation('Loading', 'repsoitory') + ' ...',
			loadingImg: getPath('WEB_LAYOUT_PATH') + getTheme() + '/images/common/action_loading.gif',
			autoChange: false
		});
		
		$("table.data_table > tbody > tr > td > input.repository_version_browser_table_id:checkbox").live('click', checkCompareCheckboxes);
	});
	
})(jQuery);