<?php

class DefaultInternshipOrganizerAgreementRelMentorTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerAgreementRelMentorTableCellRenderer()
    {
    }

    function render_cell($column, $agreement_rel_mentor)
    {
        
        $mentor = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor($agreement_rel_mentor->get_mentor_id());
        
        switch ($column->get_name())
        {
            case InternshipOrganizerMentor :: PROPERTY_LASTNAME :
                return $mentor->get_lastname();
            case InternshipOrganizerMentor :: PROPERTY_FIRSTNAME :
                return $mentor->get_firstname();
            case InternshipOrganizerMentor :: PROPERTY_TITLE :
                return $mentor->get_title();
            case InternshipOrganizerMentor :: PROPERTY_TELEPHONE :
                return $mentor->get_telephone();
            case InternshipOrganizerMentor :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $mentor->get_email() . '">' . $mentor->get_email() . '</a><br/>';
        }
    
    }

    function render_id_cell($agreement_rel_mentor)
    {
        return $agreement_rel_mentor->get_agreement_id() . '|' . $agreement_rel_mentor->get_mentor_id();
    }

}
?>