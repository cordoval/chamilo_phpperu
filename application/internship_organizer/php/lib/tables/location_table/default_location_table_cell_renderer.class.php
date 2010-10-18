<?php

/** @author Steven Willaert */

require_once dirname(__FILE__) . '/../../location.class.php';
require_once dirname(__FILE__) . '/../../region.class.php';

class DefaultInternshipOrganizerLocationTableCellRenderer extends ObjectTableCellRenderer
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
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
                return $location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE);
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
                return $location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_CITY_NAME);
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