<?php

require_once require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/viewer.class.php';

class InternshipOrganizerAgreementManagerMomentRightsEditorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $moments = Request :: get(self :: PARAM_MOMENT_ID);
        
        $this->set_parameter(self :: PARAM_MOMENT_ID, $moments);
        
        if ($moments && ! is_array($moments))
        {
            $moments = array($moments);
        }
        
        $locations = array();
        
        foreach ($moments as $moment_id)
        {
            
            $moment = InternshipOrganizerDataManager :: get_instance()->retrieve_moment($moment_id);
            if ($this->get_user()->is_platform_admin() || $moment->get_owner() == $this->get_user_id())
            {
                $locations[] = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($moment_id, InternshipOrganizerRights :: TYPE_MOMENT);
            }
        }
        
        $agreement = $moment->get_agreement();
        
        $manager = new RightsEditorManager($this, $locations);
        $user_ids = $agreement->get_user_ids(InternshipOrganizerUserType :: get_user_types());
        
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
        return InternshipOrganizerRights :: get_available_rights_for_moments();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $agreement_id = Request :: get(self :: PARAM_AGREEMENT_ID);
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS)), Translation :: get('ViewInternshipOrganizerAgreement')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID, self :: PARAM_MOMENT_ID);
    }

}
?>