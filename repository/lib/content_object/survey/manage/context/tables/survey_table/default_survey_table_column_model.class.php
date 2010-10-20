<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey.class.php';

class DefaultSurveyTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSurveyTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns());
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(Survey :: PROPERTY_TITLE, true);
        $columns[] = new ObjectTableColumn(Survey :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>