<?php

class InternshipOrganizerPeriodManagerRightsEditorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $periods = Request :: get(self :: PARAM_PERIOD_ID);
        
        $this->set_parameter(self :: PARAM_PERIOD_ID, $periods);
        
        if ($periods && ! is_array($periods))
        {
            $periods = array($periods);
        }
        
        $locations = array();
        
        foreach ($periods as $period_id)
        {
            
            $period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($period_id);
            if ($this->get_user()->is_platform_admin() || $period->get_owner() == $this->get_user_id())
            {
                $locations[] = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($period_id, InternshipOrganizerRights :: TYPE_PERIOD);
            }
        }
        
        $manager = new RightsEditorManager($this, $locations);
        $user_ids = $period->get_user_ids(InternshipOrganizerUserType :: get_user_types());
        if (count($user_ids) > 0)
        {
            $manager->limit_users($user_ids);
        }
        else
        {
            $manager->limit_users(array(0));
        }
        
        $manager->set_modus(RightsEditorManager :: MODUS_USERS);
        $manager->run();
    }

    function get_available_rights()
    {
        
        return InternshipOrganizerRights :: get_available_rights_for_periods();
    
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), Translation :: get('BrowseInternshipOrganizerPeriods')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PERIOD_ID);
    }

}
?>