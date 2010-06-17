<?php

class DefaultInternshipOrganizerPeriodGroupTableCellRenderer extends ObjectTableCellRenderer
{
	
	function DefaultInternshipOrganizerPeriodGroupTableCellRenderer()
	{
	}
	
	function render_cell($column, $period_rel_group)
	{
		
		$group = GroupDataManager::get_instance()->retrieve_group($period_rel_group->get_group_id());
		
		switch ($column->get_name())
			{
				case InternshipOrganizerPeriodRelGroup :: PROPERTY_USER_TYPE :
					return InternshipOrganizerUserType::get_user_type_name($period_rel_group->get_user_type());
				case Group :: PROPERTY_NAME :
					return $group->get_name();
				case Group :: PROPERTY_DESCRIPTION :
					return $group->get_description();
				
			}
		
	}
	
	function render_id_cell($period_rel_group){
		return $period_rel_group->get_period_id().'|'.$period_rel_group->get_user_type().'|'.$period_rel_group->get_group_id();
	}
	
}
?>