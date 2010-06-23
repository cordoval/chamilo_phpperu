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
        $region = $location->get_region();
    	
        switch ($column->get_name())
        {
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $location->get_name();
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return $location->get_address();
                
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
            	$zip_code = $region->get_zip_code();
            	return $zip_code;
            
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
            	$city_name = $region->get_city_name();
            	return $city_name;    
                
//            case InternshipOrganizerLocation :: PROPERTY_REGION_ID :
//            	$region = $location->get_region();
//            	$region_string = $region->get_zip_code(). '  ' .$region->get_city_name();
//                return $region_string;
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