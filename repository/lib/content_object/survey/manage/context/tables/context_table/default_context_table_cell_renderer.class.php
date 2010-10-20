<?php

class DefaultSurveyContextTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyContextTableCellRenderer()
    {
    }

    function render_cell($column, $context)
    {
    	
    	if ($column->get_name() == SurveyContext :: PROPERTY_NAME)
        {
            return $context->get_name();
        }
        else
        {
            return $context->get_additional_property($column->get_name());
        }
    
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>