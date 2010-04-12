/*global $, window, handleResize, getWindowHeight, reinit, document, jQuery, destroy, setTimeout, clearTimeout */

$(function() {
	var windowHeight = getWindowHeight(), resizeTimer = null;

	function hideMessages() {
		setTimeout("$('.normal-message').fadeOut(500);", 5000);
		setTimeout("$('.error-message').fadeOut(500);", 1000);
		setTimeout("$('.warning-message').fadeOut(500);", 15000);
	}

	function addClosers() {
		// Normal messages
		$(".normal-message").bind('mouseenter', function(e) {
			$("#closeMessage", this).attr('class', 'close_normal_message');
		});
		
		// Warning messages
		$(".warning-message").bind('mouseenter', function(e) {
			$("#closeMessage", this).attr('class', 'close_warning_message');
		});
		
		// Error messages
		$(".error-message").bind('mouseenter', function(e) {
			$("#closeMessage", this).attr('class', 'close_error_message');
		});
		
		// General functionality
		$(".normal-message, .warning_message, .error_message").bind('mouseleave', function(e) {
			$("#closeMessage", this).attr('class', 'close_message');
		});
		$("#closeMessage").bind('click', function(e) {
			$(this).parent().fadeOut(500);
		});
	}

	function placeFooter() {
		var htmlHeight = $("body").outerHeight();

		if (htmlHeight > windowHeight) {
			$("#footer").css("position", "static");
			$("#footer").css("bottom", "");
			$("#footer").css("left", "");
			$("#footer").css("right", "");

			$("#main").css("margin-bottom", "0px");
		} else {
			$("#footer").css("position", "fixed");
			$("#footer").css("bottom", "0px");
			$("#footer").css("left", "0px");
			$("#footer").css("right", "0px");

			$("#main").css("margin-bottom", "30px");
		}

		$(window).bind('resize', handleResize);
	}

	function handleResize() {
		var currentHeight = getWindowHeight();

		if (resizeTimer) {
			clearTimeout(resizeTimer);
		}

		if (windowHeight !== currentHeight) {
			reinit();
		}
	}

	function getWindowHeight() {
		if (window.innerHeight) {
			return window.innerHeight;
		} else if (document.documentElement) {
			return document.documentElement.offsetHeight;
		}
	}

	function reinit() {
		windowHeight = getWindowHeight();
		destroy();
		placeFooter();
	}

	function destroy() {
		$(window).unbind('resize', handleResize);
	}

	$(document).ready(function() {
		addClosers();
		$(".minidropnav").tabula( {
			cycle : false,
			follow : false,
			nextButton : ">>",
			prevButton : "<<"
		});
		// hideMessages();

			// placeFooter();

			// $('iframe:not(.processed)').TextAreaResizer();
		});

});