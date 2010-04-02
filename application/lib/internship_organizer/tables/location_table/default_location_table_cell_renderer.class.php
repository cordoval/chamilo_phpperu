<?php

require_once dirname(__FILE__) . '/../../location.class.php';

class DefaultInternshipOrganizerLocationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerLocationTableCellRenderer()
    {
    }

    function render_cell($column, $location)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $location->get_name();
            case InternshipOrganizerLocation :: PROPERTY_CITY :
                return $location->get_city();
            case InternshipOrganizerLocation :: PROPERTY_STREET :
                return $location->get_street() . ' ' . $location->get_street_number();
            
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