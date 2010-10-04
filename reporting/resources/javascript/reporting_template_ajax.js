(function ($) {
	function hoverInItem() {
		//$(this).children("div.reporting_header").children("div.reporting_header_displaymode").fadeIn(150);
        $(this).children("div.reporting_footer").children("div.reporting_footer_export").fadeIn(150);
	}

	function hoverOutItem() {
		//$(this).children("div.reporting_header").children("div.reporting_header_displaymode").fadeOut(150);
        $(this).children("div.reporting_footer").children("div.reporting_footer_export").fadeOut(150);
	}

	function bindIcons() {
        //$("div.reporting_header_displaymode").hide();
        $("div.reporting_footer_export").hide();

		$("div.reporting_block").unbind();
		$("div.reporting_block").bind('mouseenter', hoverInItem);
		$("div.reporting_block").bind('mouseleave', hoverOutItem);
	}

	$(document).ready(function () {
		bindIcons();
	});

})(jQuery);