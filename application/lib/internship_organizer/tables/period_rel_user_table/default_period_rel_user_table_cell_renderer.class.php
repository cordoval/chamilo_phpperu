<?php

class DefaultInternshipOrganizerPeriodRelUserTableCellRenderer implements ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerPeriodRelUserTableCellRenderer()
    {
    }

    function render_cell($column, $period_rel_user)
    {
        
        $user = UserDataManager :: get_instance()->retrieve_user($period_rel_user->get_user_id());
        
        switch ($column->get_name())
        {
            case InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE :
                return InternshipOrganizerUserType :: get_user_type_name($period_rel_user->get_user_type());
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

    function render_id_cell($period_rel_user)
    {
        return $period_rel_user->get_period_id() . '|' . $period_rel_user->get_user_type() . '|' . $period_rel_user->get_user_id();
    }

}
?>