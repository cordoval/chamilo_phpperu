<?php

class DefaultInternshipOrganizerMentorRelLocationTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerMentorRelLocationTableCellRenderer()
    {
    }

    function render_cell($column, $mentor_rel_location)
    {
            	
    	
    	$mentor = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor($mentor_rel_location->get_mentor_id());
        
        switch ($column->get_name())
        {
           case InternshipOrganizerMentor :: PROPERTY_LASTNAME :
                return $mentor->get_lastname();
            case InternshipOrganizerMentor :: PROPERTY_FIRSTNAME :
                return $mentor->get_firstname();
            case InternshipOrganizerMentor :: PROPERTY_TITLE :
                return $mentor->get_locationname();
            case InternshipOrganizerMentor :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $mentor->get_email() . '">' . $mentor->get_email() . '</a><br/>';
        }
    
    }

    function render_id_cell($mentor_rel_location)
    {
        return $mentor_rel_location->get_mentor_id() . '|' . $mentor_rel_location->get_location_id();
    }

}
?>