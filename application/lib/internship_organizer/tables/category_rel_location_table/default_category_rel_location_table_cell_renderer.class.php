<?php
/**
 * $Id: default_category_rel_location_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerCategoryRelLocationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerCategoryRelLocationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $categoryrellocation)
//    function render_cell($column, $location)
    {
        $location_id = $categoryrellocation->get_location_id();
        $location = InternshipOrganizerDataManager :: get_instance()->retrieve_location($location_id);
        
        $organisation = $location->get_organisation();
        $region = $location->get_region();
//        
//        $location_id = $location->get_location_id();
//        $location = InternshipOrganizerDataManager :: get_instance()->retrieve_location($location_id);
//        $organisation = $location->get_organisation();
//        $region = $location->get_region();
        
        switch ($column->get_name())
        {
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $location->get_name();
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return $location->get_address();    
            case InternshipOrganizerLocation :: PROPERTY_DESCRIPTION :
                return $location->get_description();
//            case InternshipOrganizerLocation :: PROPERTY_REGION_ID :
//            	$region = $location->get_region();
//            	$region_string = $region->get_zip_code(). '  ' .$region->get_city_name();
//                return $region_string;
            	
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
            	$region = $location->get_region();
            	$zip_code = $region->get_zip_code();
            	return $zip_code;
            
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
            	$city_name = $region->get_city_name();
            	return $city_name;
            	
//            	. '  ' .$region->get_city_name();
//                return $region_string;
//            	return $    
                
//            	$city_string = $region->get_zip_code() . '  ' . $region->get_city_name();
//                return $city_string;
//                
//                case InternshipOrganizerLocation :: PROPERTY_REGION_ID :
            	
            //case InternshipOrganizerLocation :: PROPERTY_CITY :
              //  return $location->get_city();
           // case InternshipOrganizerLocation :: PROPERTY_STREET :
           //     return $location->get_street();
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