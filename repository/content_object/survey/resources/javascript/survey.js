( function($) 
{
	function processAnswers(e, ui)
	{
		var surveyPublicationId, surveyPageId,checkedQuestions, checkedQuestionResults, displayResult;
		
		surveyPublicationId = $.query.get('publication_id');
		
		surveyPageId = $("input[name=survey_page]").val();
		
		checkedQuestions = $(".question input:checked");
		checkedQuestionResults = {};
		
		checkedQuestions.each(function (i)
		{
			checkedQuestionResults[$(this).attr('type') +  '_' + $(this).attr('name')] = $(this).val();
		});
		
		displayResult = doAjaxPost("./repository/content_object/survey/php/ajax/survey.php", {"survey_page" : surveyPageId, "survey_publication" : surveyPublicationId, "results" : $.json.serialize(checkedQuestionResults)});
		
//		alert(displayResult);
		
		var questionVisibilities = eval('(' + displayResult + ')');
		
		$.each(questionVisibilities, function (questionId, questionVisible)
		{
			if (!questionVisible)
			{
				$("div#" + questionId).hide();
			}else{
				$("div#" + questionId).removeAttr("style");
			}
//			alert(element);
//			alert(i);
		});
	}
	
	$(document).ready( function() 
	{
		$(".question input").live('click', processAnswers);
	});
	
})(jQuery);