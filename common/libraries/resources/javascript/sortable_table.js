(function ($)
{
	function form_submitted(evt, ui)
	{
		var table_name = $(this).attr('name');
		table_name = table_name.substr(5);
		
		var result = true;
		
		if(!any_checkbox_checked(table_name))
			return false;
		
		var actions = $('#actions_' + table_name);
		var selectedOption = $('option:selected', actions);
		var selectedClass = selectedOption.attr('class');
		
		var selectedValue = selectedOption.attr('value');
		selectedValue = underscores_to_camelcase(selectedValue);

		if(selectedClass == 'confirm')
		{
			return confirm(getTranslation(selectedValue + 'Confirm'));
		}
	}
	
	function any_checkbox_checked(table_name)
	{
		var result = false;
		$('.' + table_name + '_id:checked').each(function () 
		{ 
            result = true;
            return false;
        });
	
		return result;
	}
	
	$(document).ready(function () 
    {
		$('.table_form').live('submit', form_submitted);
	});
    
    function ucfirst(string)
    {
    	var f = string.charAt(0).toUpperCase();
		return f + string.substr(1);
    }
    
    function underscores_to_camelcase(string)
    {
    	var array = string.split('_');
    	var str = '';
    	
    	for(i = 0; i < array.length; i++)
    	{
    		str += ucfirst(array[i]);
    	}
    	
    	return str;
    }

})(jQuery);

   



