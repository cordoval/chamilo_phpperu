(function ($) {
	
	var maxBlockHeight = 0, maxComplexBlockHeight = 0;
	
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
	});
	
})(jQuery);