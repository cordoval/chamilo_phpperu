/**
 * Copyright (c) 2010, Sven Vanpoucke, Chamilo tree menu in jQuery plugin 
 */

(function($){
	$.fn.extend({ 
		tree_menu: function(options) {

			//Settings list and the default values
			var defaults = {
					search: ''
			};
			
			var settings = $.extend(defaults, options), self = $(this);
			
			function collapseItem(e) 
			{
				$("ul:first", $(this).parent()).hide();
				if ($(this).hasClass("lastCollapse"))
				{
					$(this).removeClass("lastCollapse");
					$(this).addClass("lastExpand");
				}
				else if ($(this).hasClass("collapse"))
				{
					$(this).removeClass("collapse");
					$(this).addClass("expand");
				}
			}
			
			function expandItem(e) 
			{
				$("ul:first", $(this).parent()).show();
				if ($(this).hasClass("lastExpand"))
				{
					$(this).removeClass("lastExpand");
					$(this).addClass("lastCollapse");
				}
				else if ($(this).hasClass("expand"))
				{
					$(this).removeClass("expand");
					$(this).addClass("collapse");
				}
			}
			
			function processTree()
			{
				$("ul li:last-child > div").addClass("last"); 
				$("ul li:last-child > div.expand").addClass("lastExpand");
				$("ul li:last-child > div.expand").removeClass("expand");
				$("ul li:last-child > ul").css("background-image", "none");
				
				$("ul li:not(:last-child):has(ul) > div").addClass("collapse");
				$("ul li:last-child:has(ul) > div").addClass("lastCollapse");
				
				$("ul li:has(ul) > div").toggle(collapseItem, expandItem);
				$("ul li:has(ul) > div > a").click(function(e){e.stopPropagation();});
			}

			
			function init()
			{
				processTree();
			}
			
			return this.each(init);
    	}
	});
})(jQuery);
