<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\ObjectTableCellRenderer;

class DefaultSurveyTemplateTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }

    function render_cell($column, $template)
    {
        switch ($column->get_name())
        {
            case SurveyTemplate :: PROPERTY_NAME :
                return $template->get_name();
            case SurveyTemplate :: PROPERTY_DESCRIPTION :
                return $template->get_description();
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