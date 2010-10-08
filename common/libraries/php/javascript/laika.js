$(function () {

	function showInformation(e, ui)
	{
		e.preventDefault();
		var id = $(this).attr("id");
		
		$.post("./application/laika/php/ajax/scale_information.php", {scale: id}, function (data) {
			
			var message, loading;
			
			message = '<div class="scaleInformationBox"><h4>' + data.subtitle + '</h4><h2>' + data.title + '</h2>' + data.message + '<div class="clear"></div></div>';
			
			loading = $.modal(message, {
				overlayId: 'laikaOverlay',
				containerId: 'laikaContainer',
				opacity: 75
			});
			
//			loading.dialog.container.append($(loading.opts.closeHTML).addClass(loading.opts.closeClass));
//			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
//			handleLoadingBox(loading);
		}, "json");
	}
	
	$(document).ready(function () {
		$("a.showInfo").live('click', showInformation);
	});

});