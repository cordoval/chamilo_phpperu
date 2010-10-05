<?php

require_once Path :: get_application_path() . 'internship_organizer/php/organisation_manager/component/browser.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/organisation_manager/component/viewer.class.php';

class InternshipOrganizerOrganisationManagerLocationRightsEditorComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $organisation_locations = Request :: get(self :: PARAM_LOCATION_ID);
        
        $this->set_parameter(self :: PARAM_LOCATION_ID, $organisation_locations);
        
        if ($organisation_locations && ! is_array($organisation_locations))
        {
            $organisation_locations = array($organisation_locations);
        }
        
        $locations = array();
        $organisation_ids = array();
        foreach ($organisation_locations as $organisation_location_id)
        {
            
            $organisation_location = InternshipOrganizerDataManager :: get_instance()->retrieve_location($organisation_location_id);
            if ($this->get_user()->is_platform_admin() || $organisation_location->get_owner_id() == $this->get_user_id())
            {
                $locations[] = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($organisation_location_id, InternshipOrganizerRights :: TYPE_LOCATION);
            	$organisation_ids[] = $organisation_location->get_organisation_id();
            }
        }
        
        $user_ids = array();
        
        $condition = new InCondition(InternshipOrganizerOrganisationRelUser::PROPERTY_ORGANISATION_ID, $organisation_ids);
		$organisations_rel_users = InternshipOrganizerDataManager::get_instance()->retrieve_organisation_rel_users($condition);
        while($organisations_rel_user = $organisations_rel_users->next_result()){
        	$user_ids[] = $organisations_rel_user->get_user_id();
        }
        
        $manager = new RightsEditorManager($this, $locations);
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
        
        return InternshipOrganizerRights :: get_available_rights_for_locations();
    
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_LOCATIONS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID);
    }

}
?>