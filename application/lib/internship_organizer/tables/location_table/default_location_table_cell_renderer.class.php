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

    function render_cell($column, $category_rel_location)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_NAME);
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_ADDRESS);
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
                return $category_rel_location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE);
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
                return $category_rel_location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_CITY_NAME);
            case InternshipOrganizerLocation :: PROPERTY_TELEPHONE :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_TELEPHONE);
            case InternshipOrganizerLocation :: PROPERTY_FAX :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_FAX);
            case InternshipOrganizerLocation :: PROPERTY_EMAIL :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_EMAIL);
            case InternshipOrganizerLocation :: PROPERTY_DESCRIPTION :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION);
            
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