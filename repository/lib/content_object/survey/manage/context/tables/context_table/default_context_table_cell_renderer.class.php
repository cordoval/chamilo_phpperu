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
    	
//    	switch ($column->get_name())
//        {
//            case SurveyContextRegistration :: PROPERTY_NAME :
//                return $context_registration->get_name();
//            case SurveyContextRegistration :: PROPERTY_DESCRIPTION :
//                return $context_registration->get_description();
//            default :
//                return '&nbsp;';
//        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>