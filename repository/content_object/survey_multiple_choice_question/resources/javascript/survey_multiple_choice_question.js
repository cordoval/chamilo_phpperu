/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost */

$(function ()
{
	var skippedOptions = 0;
	
    function getDeleteIcon()
    {
		return $('.data_table > tbody > tr:first td:last .remove_option').attr('src').replace('_na.png', '.png');
    }
    
	function processOptions()
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
			var rowName, id, appendField;
		    
			rowName = $(this).attr('id');
		    id = rowName.substr(7);
		    appendField = deleteField.replace(/\$option_number/g, id);
	
		    $('.remove_option', this).remove();
		    $('td:last', this).append(appendField);
		});
	}
	
	function convertType(ev, ui) 
	{
		ev.preventDefault();
		
		var answerType = parseInt($('#mc_answer_type').val(), 10),
			newLabel = getTranslation('SwitchToCheckboxes', 'repository'),
			newType = 1,
			counter = 0;
		
		if (answerType === 1)
		{
			newType = 2;
			newLabel = getTranslation('SwitchToRadioButtons', 'repository');
		}
		
		$('#mc_answer_type').val(newType);
		setMemory('mc_answer_type', newType);
		
		$('.switch').val(newLabel);
		$('.switch').text(newLabel);
	}
	
	function removeOption(ev, ui)
	{
		ev.preventDefault();
		
		var tableBody = $(this).parent().parent().parent(),
			id = $(this).attr('id'),
			row = 0,
			answer_type = $('#mc_answer_type').val(),
			rows;
		
		id = id.replace('remove_', '');
		
		destroyHtmlEditor('value['+ id +']');
		$('tr#option_' + id, tableBody).remove();
		
		rows = $('tr.row_odd, tr.row_even', tableBody);
		
		doAjaxPost("./common/javascript/ajax/mc_question.php", { action: 'skip_option', value: id });
		
		rows.each(function ()
		{			
			var row_class = (row % 2 === 0 ? 'row_even' : 'row_odd');
			$(this).attr('class', row_class);
			row += 1;
		});
		
		skippedOptions += 1;
		
		processOptions();
	}
	
	function addOption(ev, ui)
	{
		ev.preventDefault();
		
		var numberOfOptions = $('#mc_number_of_options').val(),
			newNumber = (parseInt(numberOfOptions, 10) + 1),
			mcAnswerType = $('#mc_answer_type').val(),
			rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even' : 'row_odd',
			id = name,
			value = 1,
			fieldAnswer, fieldDelete, string,
			parameters, editorNameAnswer;
		
		setMemory('mc_number_of_options', newNumber);
		
		$('#mc_number_of_options').val(newNumber);
		
		if (mcAnswerType === 'radio')
		{
			name = 'correct';
			value = numberOfOptions;
		}
		
		parameters = { "width" : "100%", "height" : "65", "toolbar" : "RepositoryQuestion", "collapse_toolbar" : true };
		editorNameAnswer = 'value[' + numberOfOptions + ']';
		
		fieldAnswer = renderHtmlEditor(editorNameAnswer, parameters);
		fieldDelete = '<input id="remove_' + numberOfOptions + '" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[' + numberOfOptions + ']" />';
		
		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass + '"><td>' + fieldAnswer + '</td><td>' + fieldDelete + '</td></tr>';
		
		$('.data_table > tbody').append(string);
		
		processOptions();
	}

	$(document).ready( function() 
	{
		$('.change_answer_type').live('click', convertType);
		$('.remove_option').live('click', removeOption);
		$('.add_option').live('click', addOption);
	});
	
});