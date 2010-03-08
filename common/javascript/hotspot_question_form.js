/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost, serialize, unserialize */

$(function ()
{
	//var colours = ['#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893'],
	var colours = ['#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff'],
		offset,
		currentPolygon = null,
		positions = [],
		skippedOptions = 0;
	
	/********************************
	 * Functionality to draw hotspots
	 ********************************/

	function redrawPolygon()
	{
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();

		$('#hotspot_image').fillPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_fill_' + currentPolygon, color: colours[currentPolygon], alpha: 0.5});
		$('#hotspot_image').drawPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_line_' + currentPolygon, color: colours[currentPolygon], stroke: 1, alpha: 0.9});
	}
	
	function setCoordinates()
	{
		var coordinatesField = $('input[name="coordinates[' + currentPolygon + ']"]'),
			coordinatesData = [],
			currentCoordinates = positions[currentPolygon];
		
		$.each(currentCoordinates.X, function (index, item)
		{
			coordinatesData.push([item, currentCoordinates.Y[index]]);
		});
		
		coordinatesField.val((serialize(coordinatesData)));
	}

	function getCoordinates(ev, ui)
	{
		if (currentPolygon !== null)
		{
			var pX, pY;
	
			offset = $('#hotspot_image').offset();
			
			pX = ev.pageX - offset.left;
			pY = ev.pageY - offset.top;
			pX = pX.toFixed(0);
			pY = pY.toFixed(0);
			positions[currentPolygon].X.push(parseInt(pX, 10));
			positions[currentPolygon].Y.push(parseInt(pY, 10));
	
			redrawPolygon();
			setCoordinates();
		}
	}
	
	function resetPolygonObject(id)
	{
		currentPolygon = id;
		
		positions[currentPolygon] = {};
		positions[currentPolygon].X = [];
		positions[currentPolygon].Y = [];
		
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();
	}
	
	function resetPolygon(ev, ui)
	{
		ev.preventDefault();
		var id = $(this).attr(('id')).replace('reset_', '');
		$('#hotspot_marking .colour_box').css('background-color', colours[id]);
		resetPolygonObject(id);
	}
	
	function editPolygon(ev, ui)
	{
		ev.preventDefault();
		var id = $(this).attr(('id')).replace('edit_', '');
		$('#hotspot_marking .colour_box').css('background-color', colours[id]);
		resetPolygonObject(id);
	}
	
	function initializePolygons()
	{
		$('input[name*="coordinates"]').each(function (i)
		{
			var fieldName = $(this).attr('name'),
				id = fieldName.substr(12, fieldName.length - 13),
				fieldValue = $(this).val();
			
			if (fieldValue !== '')
			{
				fieldValue = unserialize(fieldValue);
				
				currentPolygon = id;
				resetPolygonObject(id);
				
				$.each(fieldValue, function (index, item)
				{
					positions[id].X.push(item[0]);
					positions[id].Y.push(item[1]);
				});
				
				redrawPolygon();
			}
		});
	}
	
	/***************************************
	 * Functionality to add / remove options
	 ***************************************/
	
	function getEditIcon()
	{
		return $('.data_table > tbody > tr:first > td:last .edit_option').attr('src');
	}
	 
	function getResetIcon()
	{
		return $('.data_table > tbody > tr:first > td:last .reset_option').attr('src');
	}
	
	function getDeleteIcon()
	{
		return $('.data_table > tbody > tr:first > td:last .remove_option').attr('src').replace('_na.png', '.png');
	}
   
	function processOptions()
	{
		var deleteImage, deleteField, rows;
		
		deleteImage = '<img class="remove_option" src="' + getDeleteIcon().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_$option_number" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[$option_number]" />';
		rows = $('.data_table > tbody > tr');
		
		if (rows.size() <= 1)
		{
		    deleteField = deleteImage;
		}
		
		rows.each(function ()
		{
			var weightField, weightFieldName, id, appendField;
		    
			weightField = $('input[name*="option_weight"]', this);
			weightFieldName = weightField.attr('name');
		    id = weightFieldName.substr(14, weightFieldName.length - 15);
		    appendField = deleteField.replace(/\$option_number/g, id);
	
		    $('.remove_option', this).remove();
		    $('td:last', this).append(appendField);
		});
	}
	
	function removeOption(ev, ui)
	{
		ev.preventDefault();
		
		var tableBody = $(this).parent().parent().parent(),
			id = $(this).attr('id'),
			row = 0,
			rows;
		
		id = id.replace('remove_', '');
		$('tr#option_' + id, tableBody).remove();
		$('input[name="coordinates[' + id + ']"]').remove();
		
		rows = $('tr', tableBody);
		
		doAjaxPost("./common/javascript/ajax/mc_question.php", { action: 'skip_option', value: id });
		
		rows.each(function ()
		{
			var row_class = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', row_class);
			row += 1;
		});
		
		skippedOptions += 1;
		processOptions();
		
		// Delete the hotspots visually on the image
		$('.polygon_fill_' + id, $('#hotspot_image')).remove();
		$('.polygon_line_' + id, $('#hotspot_image')).remove();
	}
	
	function addOption(ev, ui)
	{
		ev.preventDefault();
		
		var numberOfOptions = $('#mc_number_of_options').val(),
			newNumber = (parseInt(numberOfOptions, 10) + 1),
			rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even' : 'row_odd',
			name = 'correct[' + numberOfOptions + ']',
			id = name,
			fieldColour, fieldCoordinates, fieldAnswer, fieldComment,
			fieldScore, fieldEdit, fieldReset, fieldDelete, string,
			parameters, editorNameAnswer, editorNameComment;
		
		setMemory('mc_number_of_options', newNumber);
		
		$('#mc_number_of_options').val(newNumber);
		
		parameters = { "width" : "100%", "height" : "65", "toolbar" : "RepositoryQuestion", "collapse_toolbar" : true };
		editorNameAnswer = 'answer[' + numberOfOptions + ']';
		editorNameComment = 'comment[' + numberOfOptions + ']';
		
		fieldColour = '<div class="colour_box" style="background-color: ' + colours[numberOfOptions] + ';"></div>';
		fieldCoordinates = '<input name="coordinates[' + numberOfOptions + ']" type="hidden" value="" />';
		fieldAnswer = renderHtmlEditor(editorNameAnswer, parameters);
		fieldComment = renderHtmlEditor(editorNameComment, parameters);
		fieldScore = '<input class="input_numeric" type="text" value="1" name="option_weight[' + numberOfOptions + ']" size="2" />';
		fieldEdit = '<input id="edit_' + numberOfOptions + '" class="edit_option" type="image" src="' + getEditIcon() + '" name="edit[' + numberOfOptions + ']" />&nbsp;&nbsp;';
		fieldReset = '<input id="reset_' + numberOfOptions + '" class="reset_option" type="image" src="' + getResetIcon() + '" name="reset[' + numberOfOptions + ']" />&nbsp;&nbsp;';
		fieldDelete = '<input id="remove_' + numberOfOptions + '" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[' + numberOfOptions + ']" />';
		
		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass + '"><td>' + fieldColour + fieldCoordinates + '</td><td>' + fieldAnswer + '</td><td>' + fieldComment + 
				 '</td><td>' + fieldScore + '</td><td>' + fieldEdit + fieldReset + fieldDelete + '</td></tr>';
		
		$('.data_table > tbody').append(string);
		
		processOptions();
		
		// Prepare the positions array and hotspots image
		$('#hotspot_marking .colour_box').css('background-color', colours[numberOfOptions]);
		resetPolygonObject(numberOfOptions);
	}
	
	function setHotspotImage(ev, ui)
	{
		var learningObjectId = $(this).attr('id').replace('lo_', ''),
			imageProperties;
		$('input[name="image_object"]').val(learningObjectId);
		
		imageProperties = doAjaxPost("./common/javascript/ajax/image_properties.php", { content_object: learningObjectId });
		imageProperties = eval('(' + imageProperties + ')');
		
		$('#hotspot_image').css('width', imageProperties.width + 'px');
		$('#hotspot_image').css('height', imageProperties.height + 'px');
		$('#hotspot_image').css('background-image', 'url(' + imageProperties.webPath + ')');
		
		$('#hotspot_select').hide();
		$('#hotspot_options').show();
	}

	$(document).ready(function ()
	{
		// We've got JavaScript so we hide the warning message
		$('#hotspot_javascript').hide();
		$('#hotspot_select').show();
		
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
				imageProperties = eval('(' + response + ')');
				
				$('input[name="image_object"]').val(imageProperties.id);
				
				$('#hotspot_image').css('width', imageProperties.width + 'px');
				$('#hotspot_image').css('height', imageProperties.height + 'px');
				$('#hotspot_image').css('background-image', 'url(' + imageProperties.webPath + ')');
				
				$('#hotspot_select').hide();
				$('#hotspot_options').show();
			}
		});
		
		var value = $('input[name="image_object"]').val();
		if(value != 'Image object')
		{
			imageProperties = doAjaxPost("./common/javascript/ajax/image_properties.php", { content_object: value });
			imageProperties = eval('(' + imageProperties + ')');
			
			$('#hotspot_image').css('width', imageProperties.width + 'px');
			$('#hotspot_image').css('height', imageProperties.height + 'px');
			$('#hotspot_image').css('background-image', 'url(' + imageProperties.webPath + ')');
			
			$('#hotspot_select').hide();
			$('#hotspot_options').show();
		}
		
		// Initialize possible existing polygons
		initializePolygons();
		
		// Bind clicks on the edit and reset buttons
		$('input[name*="edit"]').live('click', editPolygon);
		$('input[name*="reset"]').live('click', resetPolygon);

		// Bind clicks on the image
		$('#hotspot_image').click(getCoordinates);
		
		// Bind actions to option management buttons
		$('.remove_option').live('click', removeOption);
		$('.add_option').live('click', addOption);
		
		// Process image selection
		$('.inactive_elements a:not(.disabled, .category)').live('click', setHotspotImage);
	});

});