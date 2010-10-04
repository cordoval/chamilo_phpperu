( function($) 
{
	var toggleList = function(e, ui) 
	{
		$("#feedbacklist").toggle();
		$("#showfeedback").toggle();
		$("#hidefeedback").toggle();
	};
	
	var toggleForm = function(e, ui) 
	{
		$("#feedbackform").toggle();
		$("#showfeedbackform").toggle();
		$("#hidefeedbackform").toggle();
	};
	
	function bindIcons() 
	{
		$("#showfeedback").unbind();
		$("#showfeedback").bind('click', toggleList);
		$("#hidefeedback").unbind();
		$("#hidefeedback").bind('click', toggleList);
		
		$("#showfeedbackform").unbind();
		$("#showfeedbackform").bind('click', toggleForm);
		$("#hidefeedbackform").unbind();
		$("#hidefeedbackform").bind('click', toggleForm);
	}
	
	$(document).ready( function() 
	{
		$("#feedbacklist").toggle();
		$("#showfeedback").toggle();
		
		$("#feedbackform").toggle();
		$("#showfeedbackform").toggle();
		
		bindIcons();
		
	});
	
})(jQuery);