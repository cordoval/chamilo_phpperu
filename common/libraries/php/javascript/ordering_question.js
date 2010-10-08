/*global $, document, FCKeditor, renderFckEditor, getPath, getTranslation, getTheme */

$(function ()
{
    var skippedOptions = 0, baseWebPath = getPath('WEB_PATH'), currentNumberOfOptions;
    
    function getDeleteIcon()
    {
		return $('.data_table > tbody > tr:first > td:last .remove_option').attr('src').replace('_na.png', '.png');
    }
    
    function getSelectOptions()
    {
		return $('.data_table > tbody > tr:first select[name*="option_order"]').html();
    }
    
    function processItems()
    {
    	var deleteImage, deleteField, rows;
    	
		deleteImage = '<img class="remove_option" src="' + getDeleteIcon().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_$option_number" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[$option_number]" />';
		rows = $('.data_table > tbody > tr');
	
		if (rows.size() <= 2)
		{
		    deleteField = deleteImage;
		}
		
		rows.each(function ()
		{
			var orderField, orderFieldName, id, appendField;
		    
			orderField = $('select[name*="option_order"]', this);
			
			if (rows.size() > currentNumberOfOptions)
			{
				orderField.append($('<option value="' + rows.size() + '">' + rows.size() + '</option>'));
			}
			else
			{
				$('option:last', orderField).remove();
			}
			orderFieldName = orderField.attr('name');
		    id = orderFieldName.substr(13, orderFieldName.length - 14);
		    appendField = deleteField.replace(/\$option_number/g, id);
	
		    $('.remove_option', this).remove();
		    $('td:last', this).append(appendField);
		});
		
		if (rows.size() > 2)
			$('.remove_option').bind('click', removeOption);
		currentNumberOfOptions = rows.size();
    }

    function removeOption(ev, ui)
    {
    	ev.preventDefault();

		var tableBody, id, rows, row, response;
	
		tableBody = $(this).parent().parent().parent();
		id = $(this).attr('id');
		id = id.replace('remove_', '');
		destroyHtmlEditor('option['+ id +']');
		$('tr#option_' + id, tableBody).remove();
	
		rows = $('.data_table > tbody > tr');
	
		row = 0;
	
		response = $.ajax({
		    type : "POST",
		    url : baseWebPath + "common/javascript/ajax/ordering_question.php",
		    data : {
				action : 'skip_option',
				value : id
		    },
		    async : false
		}).responseText;
		
		rows.each(function () {
		    var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
		    $(this).attr('class', rowClass);
		    row += 1;
		});
	
		skippedOptions += 1;
	
		processItems();
    }

    function addOption(ev, ui)
    {
		ev.preventDefault();
		
		var	numberOfOptions, newNumber, response, rowClass, id, fieldAnswer,
			fieldOrder, fieldDelete, string, parameters, editorName, highestOptionValue;
	
		numberOfOptions = $('#ordering_number_of_options').val();
		newNumber = parseInt(numberOfOptions, 10) + 1;
	
		response = $.ajax({
		    type : "POST",
		    url : baseWebPath + "common/javascript/ajax/memory.php",
		    data : {
				action : 'set',
				variable : 'ordering_number_of_options',
				value : newNumber
		    },
		    async : false
		}).responseText;
	
		$('#ordering_number_of_options').val(newNumber);
	
		rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even' : 'row_odd';
		id = 'correct[' + numberOfOptions + ']';
		
		parameters = { "width" : "100%", "height" : "65", "toolbar" : "RepositoryQuestion", "collapse_toolbar" : true };
		editorName = 'option[' + numberOfOptions + ']';
	
		fieldAnswer = renderHtmlEditor(editorName, parameters);
		fieldOrder = '<select name="option_order[' + numberOfOptions + ']">' + getSelectOptions() + '</select>';
		fieldDelete = '<input id="remove_' + numberOfOptions + '" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[' + numberOfOptions + ']" />';
		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass + '"><td>' + fieldAnswer + '</td><td>' + fieldOrder + '</td><td>' + fieldDelete + '</td></tr>';
	
		$('.data_table > tbody').append(string);
	
		processItems();
		
		highestOptionValue = $('.data_table tbody tr:first select[name*="option_order"] option:last').val();
		$('.data_table > tbody > tr:last select[name*="option_order"]').val(highestOptionValue);
    }

    $(document).ready(function ()
    {
    	currentNumberOfOptions = $('.data_table tbody tr').size();
		if($('.remove_option').length > 2)
			$('.remove_option').bind('click', removeOption);
		$('#add_option').live('click', addOption);
		//$('.data_table thead tr th:nth-child(2)').hide();
		//$('.data_table tbody tr td:nth-child(2)').hide();
    });
    
});