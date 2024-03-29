<?php 
namespace repository\content_object\survey;

use common\libraries\ObjectTableCellRenderer;

class DefaultSurveyContextRegistrationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }


    function render_cell($column, $context_registration)
    {
        switch ($column->get_name())
        {
            case SurveyContextRegistration :: PROPERTY_NAME :
                return $context_registration->get_name();
            case SurveyContextRegistration :: PROPERTY_DESCRIPTION :
                return $context_registration->get_description();
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