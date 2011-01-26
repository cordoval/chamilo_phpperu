(function($) {
	function processAnswers(e, ui) {
		var surveyPublicationId, surveyPageId, contextPath, checkedQuestions, checkedQuestionResults, answers, displayResult, ajaxUri = getPath('WEB_PATH')
		+ 'ajax.php';

		
		surveyPublicationId =$("input[name=publication_id]").val();
		surveyPageId = $("input[name=survey_page]").val();
		userId = $("input[name=user_id]").val();
		contextPath = $("input[name=context_path]").val();
			
		checkedQuestions = $(".question input:checked");
		
		checkedQuestionResults = {};
		answers = {};
		
		checkedQuestions.each(function(i) {
			checkedQuestionResults[$(this).attr('type') + '_'
					+ $(this).attr('name')] = $(this).val();
			var ids = $(this).attr('name').split('_');
			var question_id = ids[0];
			if(!answers[question_id]){
				answers[question_id] =  {};	
			}
//			alert(question_id+" "+$(this).attr('name')+" "+$(this).val());
			answers[question_id] [$(this).attr('name')]= $(this).val();
		});
		
		var openquestions = $("textarea.html_editor");
		openquestions.each(function(i){
			var name = $(this).attr('name');
			var ids = name.split('_');
			var question_id = ids[0];
			if(!answers[question_id]){
				answers[question_id] =  {};	
			}
			answers[question_id] [name]= $(this).val();
		});
		
		displayResult = doAjaxPost(
				ajaxUri, {
					"context" : "repository\\content_object\\survey",
					"method" : "proces_answer",
					"survey_page" : surveyPageId,
					"context_path" : contextPath,
					"survey_publication" : surveyPublicationId,
					"results" : $.json.serialize(checkedQuestionResults)
				});
	
		var questionVisibilities = eval('(' + displayResult + ')');
		
		$.each(questionVisibilities.properties.question_visibility, function(questionId, questionVisible) {
			if (!questionVisible) {
				var uncheckquestions = $("div#" + "survey_question_" +questionId+" input:checked");
				uncheckquestions.each(function(i){
					$(this).attr('checked', false);
				});
				
				var cleartextarea = $("textarea.html_editor[name="+questionId+"]");
				cleartextarea.each(function(i){
					$(this).empty();
				});
				
				
				$("div#" + "survey_question_" +questionId).hide();
				$("a[id="+contextPath+"]").parent().siblings().children().find("a[id="+contextPath+"_"+questionId+"]").each(function(i){
					$(this).parent().parent().remove();
				});
					
				var delete_answer = doAjaxPost(
						ajaxUri, {
							"context" : "application\\survey",
							"method" : "delete_answer",
							"context_path" : contextPath+"_"+questionId,
							"survey_publication" : surveyPublicationId
						});
			} else {
				$("div#" + "survey_question_" +questionId).removeAttr("style");
				var exist = false;
				$("a[id="+contextPath+"]").parent().siblings().children().find("a[id="+contextPath+"_"+questionId+"]").each(function(i){
					exist = true;
				});
				if(!exist){
					var href = $("a[id="+contextPath+"]").attr('href');
					href = href+"#"+questionId;
//					alert(href);		
					var result = doAjaxPost(
							ajaxUri, {
								"context" : "repository\\content_object\\survey",
								"method" : "get_question",
								"complex_question_id" : questionId,
								"context_path" : contextPath+"_"+questionId,
								"user_id" : userId
							});
					var question = eval('(' + result + ')');
					var title = question.properties.title;
					var type = question.properties.type;
//					alert(title);
//					alert(type);
//					alert("<li><div class="" ><a   id="+contextPath+"_"+questionId+" href="+href+"  >question_id="+ questionId+" "+title+"</a></div></li>");
//					$("<li><div class="" ><a   id="+contextPath+"_"+questionId+" href="+href+"  >question_id="+ questionId+" "+title+"</a></div></li>").insertAfter($("a[id="+contextPath+"]").parent().siblings().children().last());
				}else{
					var answer = answers[questionId];
					for(i in answer){
						var a = answer[i];
					}
					if(answer){
						var save_answer = doAjaxPost(
								ajaxUri, {
									"context" : "application\\survey",
									"method" : "save_answer",
									"context_path" : contextPath+"_"+questionId,
									"survey_publication" : surveyPublicationId,
									"answer" : $.json.serialize(answer)
								});
					}
				}
			}
			
		});
	}

	$(document).ready(function() {
		$(".question input").live('click', processAnswers);
	});

})(jQuery);