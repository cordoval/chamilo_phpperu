$(function ()
{
	function getMaximumDimensions(imageProperties)
	{
		if (imageProperties.width > 500 || imageProperties.height > 450)
		{
			if (imageProperties.width >= imageProperties.height)
			{
				imageProperties.thumbnailWidth = 500;
				imageProperties.thumbnailHeight = (imageProperties.thumbnailWidth / imageProperties.width) * imageProperties.height;
			}
			else
			{
				imageProperties.thumbnailHeight = 450;
				imageProperties.thumbnailWidth = (imageProperties.thumbnailHeight / imageProperties.height) * imageProperties.width;
			}
		}
		else
		{
			imageProperties.thumbnailWidth = imageProperties.width;
			imageProperties.thumbnailHeight = imageProperties.height;
		}
		
		return imageProperties;
	}
	
	function setSelectedImage(ev, ui)
	{
		ev.preventDefault();
		var contentObjectId = $(this).attr('id').replace('lo_', ''),
			imageProperties;
		$('input[name="header"]').val(contentObjectId);
		
		imageProperties = doAjaxPost("./common/javascript/ajax/image_properties.php", { content_object: contentObjectId });
		imageProperties = eval('(' + imageProperties + ')');
		imageProperties = getMaximumDimensions(imageProperties);
		
		$('#selected_image').attr('src', imageProperties.webPath);
		$('#selected_image').css('width', imageProperties.thumbnailWidth + 'px');
		$('#selected_image').css('height', imageProperties.thumbnailHeight + 'px');
		$('#image_container').show();
		
		$('#image_select').hide();
	}
	
	function resetImage(ev, ui)
	{
		ev.preventDefault();
		$('input[name="header"]').val('');
		$('#image_container').hide();
		$('#image_select').show();
	}
	
	$(document).ready(function ()
	{
		if ($('input[name="header"]').val() == '')
		{
			$('#image_select').show();
		}
		
		// Initialize the uploadify plugin
		$('#uploadify').uploadify ({
			'uploader': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify2/uploadify.swf',
			'script': getPath('WEB_PATH') + 'common/javascript/ajax/upload_image.php',
			'cancelImg': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify2/cancel.png',
			'folder': 'not_important',
			'auto': true,
			'scriptData': {'owner': getMemory('_uid')},
			onComplete: function (evt, queueID, fileObj, response, data)
			{
				imageProperties = eval('(' + response + ')');
				imageProperties = getMaximumDimensions(imageProperties);
				
				$('input[name="header"]').val(imageProperties.id);
				
				$('#selected_image').attr('src', imageProperties.webPath);
				$('#selected_image').css('width', imageProperties.thumbnailWidth + 'px');
				$('#selected_image').css('height', imageProperties.thumbnailHeight + 'px');
				$('#image_container').show();
				
				$('#image_select').hide();
			}
		});
		
		// Process image selection
		$('.inactive_elements a').die('click');
		$('.inactive_elements a').die('activate');
		$('.inactive_elements a:not(.disabled, .category)').bind('click', setSelectedImage);
		
		$("#change_image").bind('click', resetImage);
	});

});