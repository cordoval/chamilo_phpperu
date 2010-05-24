$(function ()
{
	$(document).ready(function ()
	{
		// Initialize the uploadify plugin
		$('#uploadify').fileUpload ({
			'uploader': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify/uploader-cms.swf',
			'script': getPath('WEB_PATH') + 'common/javascript/ajax/upload_image.php',
			'cancelImg': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify/cancel.png',
			//'buttonText': getTranslation('Browse', 'repository'),
			//'buttonImg': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify/button.png',
			//'rollover': true,
			'folder': 'not_important',
			'auto': true,
			//'width': 84,
			//'height': 27,
			'displayData': 'percentage',
			'scriptData': {'owner': getMemory('_uid')},
			onComplete: function (evt, queueID, fileObj, response, data)
			{
//				imageProperties = eval('(' + response + ')');
//				
//				$('input[name="image_object"]').val(imageProperties.id);
//				
//				$('#hotspot_image').css('width', imageProperties.width + 'px');
//				$('#hotspot_image').css('height', imageProperties.height + 'px');
//				$('#hotspot_image').css('background-image', 'url(' + imageProperties.webPath + ')');
//				
//				$('#hotspot_select').hide();
//				$('#hotspot_options').show();
			}
		});
	});

});