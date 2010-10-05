$(function() {
	$(document).ready(function() {
		$('.sortable_table_selection_controls_options').remove();
		var checkboxes = $('input.historic_variable_translation_browser_table_id');
		
		checkboxes.change(function() {
			if ($('input.historic_variable_translation_browser_table_id:checked').size() >= 2)
			{
				$('input.historic_variable_translation_browser_table_id:not(input.historic_variable_translation_browser_table_id:checked)').attr('disabled', '1');
			}
			else
			{
				$('input.historic_variable_translation_browser_table_id:not(input.historic_variable_translation_browser_table_id:checked)').removeAttr('disabled');
			}
		});
	});
});