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
	
		var i = 1;
		
		rows.each(function ()
		{
		    var weightField, weightFieldName, id, appendField;
		    
			weightField = $('input[name*="scores"]', this);
			weightFieldName = weightField.attr('name');
		    id = weightFieldName.substr(7, weightFieldName.length - 8);
		    appendField = deleteField.replace(/\$option_number/g, id);
	
		    $('.remove_option', this).remove();
		    $('td:last', this).append(appendField);
		    $('td:first', this).empty();
		    $('td:first', this).append(i);
		    
		    i++;
		});
		
		currentNumberOfOptions = rows.size();
    }

    function removeOption(ev, ui)
    {
    	ev.preventDefault();

		var tableBody, id, rows, row, response;
	
		tableBody = $(this).parent().parent().parent();
		id = $(this).attr('id');
		id = id.replace('remove_', '');
		$('tr#options_' + id, tableBody).remove();
	
		rows = $('tr', tableBody);
	
		row = 0;
	
		response = $.ajax({
		    type : "POST",
		    url : baseWebPath + "common/javascript/ajax/criteria.php",
		    data : {
				action : 'skip_criteria',
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
		
		var	numberOfOptions, newNumber, response, rowClass, fieldDescription,
		fieldScore, fieldDelete, string, highestOptionValue;
	
		numberOfOptions = $('#criteria_number_of_options').val();
		newNumber = parseInt(numberOfOptions, 10) + 1;

		$('#criteria_number_of_options').val(newNumber);
	
		rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even' : 'row_odd';
		
		var visibleNumber = numberOfOptions - skippedOptions + 1;
	
		fieldDescription = '<input type="text" name="options[' + numberOfOptions + ']" style="width: 99%;" />';
		fieldScore = '<input class="input_numeric" type="text" value="1" name="scores[' + numberOfOptions + ']" size="2"/>';
		fieldDelete = '<input id="remove_' + numberOfOptions + '" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[' + numberOfOptions + ']" />';
		string = '<tr id="options_' + numberOfOptions + '" class="' + rowClass + '"><td>' + visibleNumber + '</td><td>' + fieldDescription + '</td><td>' + fieldScore + '</td><td>' + fieldDelete + '</td></tr>';
	
		$('.data_table > tbody').append(string);
	
		processItems();
		
		response = $.ajax({
		    type : "POST",
		    url : baseWebPath + "common/javascript/ajax/memory.php",
		    data : {
				action : 'set',
				variable : 'criteria_number_of_options',
				value : newNumber
		    },
		    async : false
		}).responseText;
    }

    $(document).ready(function ()
    {
    	currentNumberOfOptions = $('.data_table tbody tr').size();
		$('.remove_option').live('click', removeOption);
		$('.add_option').live('click', addOption);
    });
    
});