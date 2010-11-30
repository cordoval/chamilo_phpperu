<?php 
namespace repository\content_object\survey;

use common\libraries\ObjectTableCellRenderer;

class DefaultSurveyTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }

    function render_cell($column, $survey)
    {
        
        switch ($column->get_name())
        {
            case Survey :: PROPERTY_TITLE :
                return $survey->get_title();
            case Survey :: PROPERTY_DESCRIPTION :
                return $survey->get_description();
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