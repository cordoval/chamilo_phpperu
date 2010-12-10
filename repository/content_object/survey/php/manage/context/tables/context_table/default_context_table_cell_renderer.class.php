<?php 
namespace repository\content_object\survey;

use common\libraries\ObjectTableCellRenderer;


class DefaultSurveyContextTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }

 function render_cell($column, $context)
    {
        
        if ($column->get_name() == SurveyContext :: PROPERTY_NAME)
        {
            return $context->get_name();
        }
        else 
            if ($column->get_name() == SurveyContext :: PROPERTY_ACTIVE)
            {
                if ($context->is_active())
                {
                    return 'yes';
                }
                else
                {
                    return 'no';
                }
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