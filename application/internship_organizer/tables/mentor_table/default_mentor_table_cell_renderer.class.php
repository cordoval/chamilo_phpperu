<?php

require_once dirname(__FILE__) . '/../../mentor.class.php';

class DefaultInternshipOrganizerMentorTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerMentorTableCellRenderer()
    {
    }

    function render_cell($column, $mentor_rel_location)
    {
            
        switch ($column->get_name())
        {
            case InternshipOrganizerMentor :: PROPERTY_TITLE :
                return $mentor_rel_location->get_optional_property(InternshipOrganizerMentor :: PROPERTY_TITLE);
            case InternshipOrganizerMentor :: PROPERTY_FIRSTNAME :
                return $mentor_rel_location->get_optional_property(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME);
            case InternshipOrganizerMentor :: PROPERTY_LASTNAME :
                return $mentor_rel_location->get_optional_property(InternshipOrganizerMentor :: PROPERTY_LASTNAME);
            case InternshipOrganizerMentor :: PROPERTY_EMAIL :
                return $mentor_rel_location->get_optional_property(InternshipOrganizerMentor :: PROPERTY_EMAIL);
            case InternshipOrganizerMentor :: PROPERTY_TELEPHONE :
                return $mentor_rel_location->get_optional_property(InternshipOrganizerMentor :: PROPERTY_TELEPHONE);
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