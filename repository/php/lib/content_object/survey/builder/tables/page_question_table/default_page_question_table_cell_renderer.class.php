<?php

class DefaultSurveyPageQuestionTableCellRenderer extends ObjectTableCellRenderer
{
	
	function DefaultSurveyPageQuestionTableCellRenderer()
	{
	}
	
	function render_cell($column, $complex_item)
	{
		
		$question_id = $complex_item->get_ref();
		$question = RepositoryDataManager::get_instance()->retrieve_content_object($question_id);
		
		switch ($column->get_name())
			{
				case ContentObject :: PROPERTY_TITLE :
					return $question->get_title();
				case ContentObject :: PROPERTY_DESCRIPTION :
					return $question->get_description();
				case ContentObject :: PROPERTY_TYPE :
					return Translation::get($question->get_type());
				case 'visible':
					if($complex_item->get_visible() == 1){
						return Translation :: get('QuestionVisible');
					}else{
						return Translation :: get('QuestionInVisible');
					}
				
				
			}
		
	}
	
	function render_id_cell($question){
		return $question->get_id();
	}
	
}
?>