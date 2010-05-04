<?php

class DefaultSurveyContextTemplateRelPageTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSurveyContextTemplateRelPageTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns());
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $dm = SurveyContextDataManager :: get_instance();
        $template_alias = $dm->get_alias(SurveyContextTemplate :: get_table_name());
        $page_alias = $dm->get_alias(SurveyPage :: get_table_name());
        
        
    	$columns = array();
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_NAME, true, $template_alias);
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_DESCRIPTION, true, $template_alias);
        $columns[] = new ObjectTableColumn(SurveyPage :: PROPERTY_TITLE, true, $page_alias);
        $columns[] = new ObjectTableColumn(SurveyPage:: PROPERTY_DESCRIPTION, true, $page_alias);
        
        return $columns;
    }
}
?>