/*global $, window, handleResize, getWindowHeight, reinit, document, jQuery, destroy, setTimeout, clearTimeout */

$(function () {
	var windowHeight = getWindowHeight(), resizeTimer = null;
	
	function hideMessages() {
		setTimeout("$('.normal-message').fadeOut(500);", 5000);
		setTimeout("$('.error-message').fadeOut(500);", 1000);
		setTimeout("$('.warning-message').fadeOut(500);", 15000);
	}
	
	function addClosers() {
		var closeNormalHtml, closeWarningHtml, closeErrorHtml;
		
		closeNormalHtml = '<div id="closeNormalMessage"></div>';
		closeWarningHtml = '<div id="closeWarningMessage"></div>';
		closeErrorHtml = '<div id="closeErrorMessage"></div>';
			
		$(".normal-message").append(closeNormalHtml);
		$(".normal-message").bind('mouseenter', function (e){$("#closeNormalMessage", this).fadeIn(150);});
		$(".normal-message").bind('mouseleave', function (e){$("#closeNormalMessage", this).fadeOut(150);});
		$("#closeNormalMessage").bind('click', function (e){$(".normal-message").fadeOut(500);});
		
		$(".warning-message").append(closeWarningHtml);
		$(".warning-message").bind('mouseenter', function (e){$("#closeWarningMessage", this).fadeIn(150);});
		$(".warning-message").bind('mouseleave', function (e){$("#closeWarningMessage", this).fadeOut(150);});
		$("#closeWarningMessage").bind('click', function (e){$(".warning-message").fadeOut(500);});
		
		$(".error-message").append(closeErrorHtml);
		$(".error-message").bind('mouseenter', function (e){$("#closeErrorMessage", this).fadeIn(150);});
		$(".error-message").bind('mouseleave', function (e){$("#closeErrorMessage", this).fadeOut(150);});
		$("#closeErrorMessage").bind('click', function (e){$(".error-message").fadeOut(500);});
	}
	
	function placeFooter()
	{
		var htmlHeight = $("body").outerHeight();
		
		if (htmlHeight > windowHeight)
		{
			$("#footer").css("position", "static");
			$("#footer").css("bottom", "");
			$("#footer").css("left", "");
			$("#footer").css("right", "");
			
			$("#main").css("margin-bottom", "0px");
		}
		else
		{
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
		
		if (resizeTimer)
		{
			clearTimeout(resizeTimer);
		}
		
		if (windowHeight !== currentHeight)
		{
			reinit();
		}
	}
	
	function getWindowHeight()
	{
		if (window.innerHeight)
		{
			return window.innerHeight;
		}
		else if (document.documentElement)
		{
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

	$(document).ready( function () {
		addClosers();
		$(".minidropnav").tabula({ cycle: false, follow: false, nextButton : ">>", prevButton : "<<" });
		//hideMessages();
		
		//placeFooter();
		
		//$('iframe:not(.processed)').TextAreaResizer();
	});

});