<?php

class InternshipOrganizerAgreementManagerRightsEditorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreements = Request :: get(self :: PARAM_AGREEMENT_ID);
        
        $this->set_parameter(self :: PARAM_AGREEMENT_ID, $agreements);
        
        if ($agreements && ! is_array($agreements))
        {
            $agreements = array($agreements);
        }
        
        $locations = array();
        
        foreach ($agreements as $agreement_id)
        {
            
            $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($agreement_id);
            if ($this->get_user()->is_platform_admin() || $agreement->get_owner() == $this->get_user_id())
            {
                $locations[] = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT);
            }
        }
        
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
        return InternshipOrganizerRights :: get_available_rights_for_agreements();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID);
    }
}
?>