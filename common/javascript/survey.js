( function($) 
{
	function processAnswers(e, ui)
	{
		var surveyPublicationId, checkedQuestions, checkedQuestionResults, displayResult;
		
		surveyPublicationId = $.query.get('survey_publication');
		
		checkedQuestions = $(".question input:checked");
		checkedQuestionResults = {};
		
		checkedQuestions.each(function (i)
		{
			checkedQuestionResults[$(this).attr('type') +  '_' + $(this).attr('name')] = $(this).val();
		});
		
		displayResult = doAjaxPost("./common/javascript/ajax/survey.php", {"survey_publication" : surveyPublicationId, "results" : $.json.serialize(checkedQuestionResults)});
		
		alert(displayResult);
		
//		var questionVisibilities = eval('(' + displayResult + ')');
//		
//		$.each(questionVisibilities, function (questionId, questionVisible)
//		{
//			if (!questionVisible)
//			{
//				$("div#" + questionId).hide();
//			}
////			alert(element);
////			alert(i);
//		});
	}
	
	$(document).ready( function() 
	{
		$(".question input").live('click', processAnswers);
	});
	
})(jQuery);