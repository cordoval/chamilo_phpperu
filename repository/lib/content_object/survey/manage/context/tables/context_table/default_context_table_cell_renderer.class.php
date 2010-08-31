<?php

class DefaultSurveyContextTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyContextTableCellRenderer()
    {
    }

    
    function render_cell($column, $context_registration)
    {
        
    	return $context_registration->get_additional_property($column->get_name());

    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>