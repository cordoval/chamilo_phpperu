<?php

require_once dirname(__FILE__) . '/../../survey_question.class.php';

class DefaultSurveyQuestionTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSurveyQuestionTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $content_object_alias = $rdm->get_alias(ContentObject :: get_table_name());
        
        $columns = array();
              
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true, $content_object_alias);
             
        return $columns;
    }
}
?>