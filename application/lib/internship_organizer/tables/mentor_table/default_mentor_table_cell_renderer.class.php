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

    function render_cell($column, $mentor)
    {
        	
    	switch ($column->get_name())
        {
//            case InternshipOrganizerMentor :: PROPERTY_TITLE :
//                return $mentor->get_title();
//            case InternshipOrganizerMentor :: PROPERTY_FIRSTNAME :
//                return $mentor->get_firstname();
//            case InternshipOrganizerMentor :: PROPERTY_LASTNAME :
//                return $mentor->get_lastname();
//            case InternshipOrganizerMentor :: PROPERTY_EMAIL :
//                return $mentor->get_email();
//            case InternshipOrganizerMentor :: PROPERTY_TELEPHONE :
//                return $mentor->get_telephone();
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