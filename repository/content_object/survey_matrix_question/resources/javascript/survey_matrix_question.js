/*global $, document, FCKeditor, renderFckEditor, getPath, getTranslation, getTheme, doAjaxPost, setMemory */

$(function ()
{
	var skippedOptions = 0;
	var skippedMatches = 0;
	var labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
	
    function getDeleteIconMatches()
    {
		return $('.data_table.matches > tbody > tr:first > td:last .remove_match').attr('src').replace('_na.png', '.png');
    }
    
    function getDeleteIconOptions()
    {
		return $('.data_table.options > tbody > tr:first > td:last .remove_option').attr('src').replace('_na.png', '.png');
    }
	
	function processMatches()
	{
		var deleteImage, deleteField, rows,
			counter = 0;
	
		deleteImage = '<img class="remove_match" src="' + getDeleteIconMatches().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_match_$option_number" class="remove_match" type="image" src="' + getDeleteIconMatches() + '" name="remove_match[$option_number]" />';
		rows = $('.data_table.matches > tbody > tr');
	
		if (rows.size() <= 2)
		{
			deleteField = deleteImage;
		}
		
		rows.each(function ()
		{
			var labelField, labelFieldName, id, appendField;
			
			labelField = $('input[name*="match_label"]', this);
			labelFieldName = labelField.attr('name');
			id = labelFieldName.substr(12, labelFieldName.length - 13);
			
			appendField = deleteField.replace(/\$option_number/g, id);
			
		    $('.remove_match', this).remove();
		    $('td:last', this).append(appendField);
		    $('td:first', this).html(labels[counter] + '<input type="hidden" value="' + labels[counter] + '" name="' + labelFieldName + '" />');
			
			counter += 1;
		});
	}
	
	function processOptions()
	{
		var deleteImage, deleteField, rows,
			counter = 1;
		
		deleteImage = '<img class="remove_option" src="' + getDeleteIconOptions().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_option_$option_number" class="remove_option" type="image" src="' + getDeleteIconOptions() + '" name="remove_option[$option_number]" />';
		rows = $('.data_table.options > tbody > tr');

		if (rows.size() <= 1)
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
			$('td:first', this).html(counter);
			
			counter += 1;
		});
	}
	
	function removeOption(ev, ui)
	{
		ev.preventDefault();
		
		var tableBody = $(this).parent().parent().parent(),
			id = $(this).attr('id').replace('remove_option_', ''),
			row = 0, rows;
		
		destroyHtmlEditor('value['+ id +']');
		$('tr#option_' + id, tableBody).remove();
		doAjaxPost("./common/javascript/ajax/matching_question.php", { action: 'skip_option', value: id });
		
		rows = $('tr', tableBody);
		rows.each(function ()
		{
			var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', rowClass);
			row += 1;
		});
		
		skippedOptions += 1;
		processOptions();
	}
	
	function addOption(ev, ui)
	{
		ev.preventDefault();
		
		var numberOfOptions = $('#mq_number_of_options').val(),
			numberOfMatches = $('#mq_number_of_matches').val(),
			newNumber = (parseInt(numberOfOptions, 10) + 1),
			rowClass = ((numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even' : 'row_odd'),
			fieldOption = newNumber,
			fieldAnswer, fieldMatches, fieldComment, fieldScore, fieldDelete, string,
			parameters, editorNameAnswer, editorNameComment,
			counter = 0;
		
		setMemory('mq_number_of_options', newNumber);
		
		$('#mq_number_of_options').val(newNumber);
		
		parameters = { "width" : "100%", "height" : "65", "toolbar" : "RepositoryQuestion", "collapse_toolbar" : true };
		editorNameAnswer = 'value[' + numberOfOptions + ']';
		editorNameComment = 'comment[' + numberOfOptions + ']';
			
		fieldAnswer = renderHtmlEditor(editorNameAnswer, parameters);
		fieldDelete = '<input id="remove_option_' + numberOfOptions + '" class="remove_option" type="image" src="' + getDeleteIconOptions() + '" name="remove_option[' + numberOfOptions + ']" />';
		
		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass + '"><td>' + fieldOption + '</td><td>' + fieldAnswer + '</td><td>' + fieldDelete + '</td></tr>';
		
		$('.data_table.options > tbody').append(string);
		
		processOptions();
	}
	
	function removeMatch(ev, ui)
	{
		ev.preventDefault();
		
		var tableBody = $(this).parent().parent().parent(),
			id = $(this).attr('id').replace('remove_match_', ''),
			row = 0, rows,
			selectBox;
		
		destroyHtmlEditor('match['+ id +']');
		$('tr#match_' + id, tableBody).remove();
		
		doAjaxPost("./common/javascript/ajax/matching_question.php", { action: 'skip_match', value: id });
		
		rows = $('tr', tableBody);
		rows.each(function ()
		{
			var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', rowClass);
			row += 1;
		});
		
		selectBox = $('.data_table.options select[name*="matches_to"]');
		$('option[value="' + id + '"]', selectBox).remove();
		
		selectBox.each(function ()
		{
			var counter = 0;
			$('option', this).each(function ()
			{
				$(this).text(labels[counter]);
				counter += 1;
			});
		});
		
		skippedMatches += 1;
		processMatches();
	}
	
	function addMatch(ev, ui)
	{
		ev.preventDefault();
		
		var numberOfMatches = $('#mq_number_of_matches').val(),
			newNumber = (parseInt(numberOfMatches, 10) + 1),
			rowClass = ((numberOfMatches - skippedMatches) % 2 === 0 ? 'row_even' : 'row_odd'),
			fieldOption, fieldAnswer, fieldDelete, string, selectBox,
			editorName, parameters;
		
		setMemory('mq_number_of_matches', newNumber);
		$('#mq_number_of_matches').val(newNumber);
		
		parameters = { "width" : "100%", "height" : "65", "toolbar" : "RepositoryQuestion", "collapse_toolbar" : true };
		editorName = 'match[' + numberOfMatches + ']';
		
		fieldOption = labels[newNumber] + '<input type="hidden" value="' + labels[newNumber] + '" name="match_label[' + numberOfMatches + ']" />';
		fieldAnswer = renderHtmlEditor(editorName, parameters);
		fieldDelete = '<input id="remove_match_' + numberOfMatches + '" class="remove_match" type="image" src="' + getDeleteIconMatches() + '" name="remove_match[' + numberOfMatches + ']" />';
		string = '<tr id="match_' + numberOfMatches + '" class="' + rowClass + '"><td>' + fieldOption + '</td><td>' + fieldAnswer + '</td><td>' + fieldDelete + '</td></tr>';
		
		$('.data_table.matches > tbody').append(string);
		
		selectBox = $('.data_table.options select[name*="matches_to"]');
		selectBox.append('<option value="' + numberOfMatches + '">' + labels[numberOfMatches - skippedMatches] + '</option>');
		
		processMatches();
	}
	
	function changeMatrixType(ev, ui)
	{
		ev.preventDefault();
		
		var matrixType = parseInt($('#mq_matrix_type').val(), 10),
			newType = (matrixType === 1 ? 2 : 1),
			newLabel;
		
		$('#mq_matrix_type').val(newType);
		
		if(newType === 2)
		{
			$('.option_matches').attr('multiple', 'multiple');
			newLabel = getTranslation('SwitchToSingleMatch', 'repository');
		}
		else
		{
			$('.option_matches').attr('multiple', null);
			newLabel = getTranslation('SwitchToMultipleMatches', 'repository');
		}
		
		$('.change_matrix_type').val(newLabel);
		$('.change_matrix_type').text(newLabel);
		
		setMemory('mq_matrix_type', newType);
	}

	$(document).ready( function() 
	{
		$('.remove_option').live('click', removeOption);
		$('#add_option').live('click', addOption);
		
		$('.remove_match').live('click', removeMatch);
		$('#add_match').live('click', addMatch);
		
		$('.change_matrix_type').live('click', changeMatrixType)
    });
    
});