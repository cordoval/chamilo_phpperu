<?php

class DefaultInternshipOrganizerPeriodRelGroupTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerPeriodRelGroupTableCellRenderer()
    {
    }

    function render_cell($column, $period_rel_group)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganizerPeriodRelGroup :: PROPERTY_USER_TYPE :
                return InternshipOrganizerUserType :: get_user_type_name($period_rel_group->get_user_type());
            case Group :: PROPERTY_NAME :
                return $period_rel_group->get_optional_property(Group :: PROPERTY_NAME);
            case Group :: PROPERTY_DESCRIPTION :
                return $period_rel_group->get_optional_property(Group :: PROPERTY_DESCRIPTION);
        
        }
    
    }

    function render_id_cell($period_rel_group)
    {
        return $period_rel_group->get_period_id() . '|' . $period_rel_group->get_group_id() . '|' . $period_rel_group->get_user_type();
    }

}
?>