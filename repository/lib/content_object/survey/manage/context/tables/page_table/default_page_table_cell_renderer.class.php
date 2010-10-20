<?php

class DefaultSurveyPageTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyPageTableCellRenderer()
    {
    }

    function render_cell($column, $page)
    {
            
    	switch ($column->get_name())
        {
            case SurveyPage :: PROPERTY_TITLE :
                return $page->get_title();
            case SurveyPage :: PROPERTY_DESCRIPTION :
                return $page->get_description();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>