<?php

class DefaultInternshipOrganizerPeriodRelUserTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerPeriodRelUserTableCellRenderer()
    {
    }

    function render_cell($column, $period_rel_user)
    {
                
        switch ($column->get_name())
        {
            case InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE :
                return InternshipOrganizerUserType :: get_user_type_name($period_rel_user->get_user_type());
            case User :: PROPERTY_LASTNAME :
                return $period_rel_user->get_optional_property(User :: PROPERTY_LASTNAME);
            case User :: PROPERTY_FIRSTNAME :
                return $period_rel_user->get_optional_property(User :: PROPERTY_FIRSTNAME);
            case User :: PROPERTY_USERNAME :
                return $period_rel_user->get_optional_property(User :: PROPERTY_USERNAME);
            case User :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $period_rel_user->get_optional_property(User :: PROPERTY_EMAIL) . '">' . $period_rel_user->get_optional_property(User :: PROPERTY_EMAIL) . '</a><br/>';
        }
    
    }

    function render_id_cell($period_rel_user)
    {
        return $period_rel_user->get_period_id() . '|' . $period_rel_user->get_user_type() . '|' . $period_rel_user->get_user_id();
    }

}
?>