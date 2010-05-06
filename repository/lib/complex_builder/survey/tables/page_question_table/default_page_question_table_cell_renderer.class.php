<?php

class DefaultSurveyPageQuestionTableCellRenderer implements ObjectTableCellRenderer
{
	
	function DefaultSurveyPageQuestionTableCellRenderer()
	{
	}
	
	function render_cell($column, $complex_item)
	{
		
		$complex_item = $complex_question->get_ref();
		
//		dump($complex_item);
		
		$question = RepositoryDataManager::get_instance()->retrieve_content_object($question_id);
				
		
		switch ($column->get_name())
			{
				case ContentObject :: PROPERTY_TITLE :
					return $question->get_title();
				case ContentObject :: PROPERTY_DESCRIPTION :
					return $question->get_description();
				case ContentObject :: PROPERTY_TYPE :
					return Translation::get($question->get_type());
				
			}
		
	}
	
	function render_id_cell($question){
		return $question->get_id();
	}
	
}
?>