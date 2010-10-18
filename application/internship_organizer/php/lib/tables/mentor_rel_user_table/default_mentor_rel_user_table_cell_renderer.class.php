<?php

class DefaultInternshipOrganizerMentorRelUserTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerMentorRelUserTableCellRenderer()
    {
    }

    function render_cell($column, $mentor_rel_user)
    {
        
        $user = UserDataManager :: get_instance()->retrieve_user($mentor_rel_user->get_user_id());
        
        switch ($column->get_name())
        {
           case User :: PROPERTY_LASTNAME :
                return $user->get_lastname();
            case User :: PROPERTY_FIRSTNAME :
                return $user->get_firstname();
            case User :: PROPERTY_USERNAME :
                return $user->get_username();
            case User :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a><br/>';
        }
    
    }

    function render_id_cell($mentor_rel_user)
    {
        return $mentor_rel_user->get_mentor_id() . '|' . $mentor_rel_user->get_user_id();
    }

}
?>