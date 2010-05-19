<?php

/** @author Steven Willaert */

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
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return $location->get_address();
            case InternshipOrganizerLocation :: PROPERTY_REGION_ID :
                return $location->get_region_id();
            //case InternshipOrganizerLocation :: PROPERTY_POSTCODE :
            //    return $location->get_postcode();    
            //case InternshipOrganizerLocation :: PROPERTY_CITY :
            //    return $location->get_postcode() . ' ' . $location->get_city();
            case InternshipOrganizerLocation :: PROPERTY_TELEPHONE :
                return $location->get_telephone();
            case InternshipOrganizerLocation :: PROPERTY_FAX :
                return $location->get_fax();
            case InternshipOrganizerLocation :: PROPERTY_EMAIL :
                return $location->get_email();
            case InternshipOrganizerLocation :: PROPERTY_DESCRIPTION :
                return $location->get_description();
            
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