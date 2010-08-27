<?php

class DefaultSurveyContextTemplateTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyContextTemplateTableCellRenderer()
    {
    }

    function render_cell($column, $context_registration)
    {
        switch ($column->get_name())
        {
            case SurveyContextTemplate :: PROPERTY_NAME :
                return $context_registration->get_name();
            case SurveyContextTemplate :: PROPERTY_DESCRIPTION :
                return $context_registration->get_description();
            case SurveyContextTemplate :: PROPERTY_CONTEXT_TYPE_NAME :
                return $context_registration->get_context_type_name();
            case SurveyContextTemplate :: PROPERTY_KEY :
                return $context_registration->get_key();
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