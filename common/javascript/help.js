( function($) 
{
	var handle_help = function(ev, ui) 
	{ 
	    var href = $(this).attr("href");
	   
	    var loadingHTML  = '<iframe style="margin: 20px; width: 760px; height: 560px;" src="' + href + '" frameborder="0">';
	    loadingHTML += '</iframe>';
	   
	    $.modal(loadingHTML, {
			overlayId: 'modalOverlay',
		  	containerId: 'modalContainer',
		  	opacity: 75
		});
		
		return false;
	} 

	$(document).ready( function() 
	{
		$(".help").bind('click', handle_help);
		
		$("#admin_tabs ul").css('display', 'block');
		$("#admin_tabs h2").hide();
		$("#admin_tabs").tabs();
		var tabs = $('#admin_tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
		tabs.tabs('select', tabnumber);
		
	});
	
})(jQuery);