<?php

require_once dirname(__FILE__) . '/../../moment.class.php';

class DefaultInternshipPlannerMomentTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerMomentTableCellRenderer()
    {
    }

    function render_cell($column, $moment)
    {
        
        switch ($column->get_name())
        {
            case InternshipPlannerMoment :: PROPERTY_NAME :
                return $moment->get_name();
            case InternshipPlannerMoment :: PROPERTY_CITY :
                return $moment->get_city();
            case InternshipPlannerMoment :: PROPERTY_STREET :
                return $moment->get_street() . ' ' . $moment->get_street_number();
            
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