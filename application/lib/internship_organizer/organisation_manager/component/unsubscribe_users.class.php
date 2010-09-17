<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/viewer.class.php';


class InternshipOrganizerOrganisationManagerUnsubscribeUsersComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        
        $ids = Request :: get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_REL_USER_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $organisationreluser_ids = explode('|', $id);
                $organisationreluser = InternshipOrganizerDataManager::get_instance()->retrieve_organisation_rel_user($organisationreluser_ids[0], $organisationreluser_ids[1]);
                
                if (! isset($organisationreluser))
                    continue;
                
                if ($organisationreluser_ids[0] == $organisationreluser->get_organisation_id())
                {
                    
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT,InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
                    {
                        if (! $organisationreluser->delete())
                        {
                            $failures ++;
                        }
                        else
                        {
                            //                        Event :: trigger('unsubscribe_user', 'organisation', array('target_organisation_id' => $organisationreluser->get_organisation_id(), 'target_organisation_id' => $organisationreluser->get_user_id(), 'action_user_id' => $user->get_id()));
                        }
                    }
                
                }
                else
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerOrganisationRelUserNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerOrganisationRelUsersNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerOrganisationRelUserDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerOrganisationRelUsersDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisationreluser_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_USERS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerOrganisationRelUserSelected')));
        }
    }
}
?>