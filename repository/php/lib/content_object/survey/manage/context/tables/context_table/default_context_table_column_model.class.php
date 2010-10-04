<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context.class.php';

class DefaultSurveyContextTableColumnModel extends ObjectTableColumnModel
{
    
    private $survey_context_type;

    /**
     * Constructor
     */
    function DefaultSurveyContextTableColumnModel($survey_context_type)
    {
        $this->survey_context_type = $survey_context_type;
        parent :: __construct(self :: get_default_columns());
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private function get_default_columns()
    {
        $columns = array();
		$survey_context = SurveyContext :: factory($this->survey_context_type);
        $property_names = $survey_context->get_additional_property_names();
		
       	$columns[] = new ObjectTableColumn(SurveyContext :: PROPERTY_NAME, false, null, false);
        
        foreach ($property_names as $property_name)
        {
            $columns[] = new ObjectTableColumn($property_name, true, null, false);
        }
        return $columns;
    }
}
?>