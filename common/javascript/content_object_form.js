$(function () 
{
	var timer;
	
	function check_for_existing_names(e, ui)
	{
		var title = $("#title").attr("value");

		$.post("./repository/ajax/title_exists.php", {title: title}, function (data) 
		{
			if(data)
			{
				$('#message').html(data);
			}
			else
			{
				$('#message').html('');
			}
		});
	}
	
	$(document).ready(function () 
	{
		$("#title").keypress( function() {
			clearTimeout(timer);
			timer = setTimeout(check_for_existing_names, 750);
		});
		
		if(typeof support_attachments != 'undefined')
		{
			$('#uploadify').uploadify ({
				'uploader': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify2/uploadify.swf',
				'script': getPath('WEB_PATH') + 'common/javascript/ajax/upload_image.php',
				'cancelImg': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify2/cancel.png',
				'folder': 'not_important',
				'auto': true,
				'displayData': 'percentage',
				'scriptData': {'owner': getMemory('_uid')},
				onComplete: function (evt, queueID, fileObj, response, data)
				{
					$('#tbl_attachments').trigger('update_search');
					var properties = eval('(' + response + ')');
					$('#lo_' + properties.id).trigger('activate');
				}
			});
		}
	});

})