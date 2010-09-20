<?php

class DefaultInternshipOrganizerMentorRelLocationTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerMentorRelLocationTableCellRenderer()
    {
    }

    function render_cell($column, $mentor_rel_location)
    {
            	
// 	dump($mentor_rel_location);
//    	
//    	$mentor = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor($mentor_rel_location->get_mentor_id());
        
        switch ($column->get_name())
        {
           case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $mentor_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_NAME);
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return  $mentor_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_ADDRESS);
           case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
               return $mentor_rel_location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_CITY_NAME);
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
               return $mentor_rel_location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE);   
        }
    
    }

    function render_id_cell($mentor_rel_location)
    {
        return $mentor_rel_location->get_mentor_id() . '|' . $mentor_rel_location->get_location_id();
    }

}
?>