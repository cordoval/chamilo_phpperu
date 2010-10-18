<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/mentor_viewer.class.php';


class InternshipOrganizerOrganisationManagerUnsubscribeMentorUserComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
               
        $ids = Request :: get(self :: PARAM_MENTOR_REL_USER_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $mentor_rel_user_ids = explode('|', $id);
                $mentor_rel_user = InternshipOrganizerDataManager::get_instance()->retrieve_mentor_rel_user($mentor_rel_user_ids[0], $mentor_rel_user_ids[1]);
                
                if (! isset($mentor_rel_user))
                    continue;
                
                if ($mentor_rel_user_ids[0] == $mentor_rel_user->get_mentor_id())
                {
                    
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT,InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        if (! $mentor_rel_user->delete())
                        {
                            $failures ++;
                        }
                        else
                        {
                            //                        Event :: trigger('unsubscribe_user', 'mentor', array('target_mentor_id' => $mentor_rel_user->get_mentor_id(), 'target_mentor_id' => $mentor_rel_user->get_user_id(), 'action_user_id' => $user->get_id()));
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
                    $message = 'SelectedInternshipOrganizerMentorRelUserNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerMentorRelUsersNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerMentorRelUserDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerMentorRelUsersDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_MENTOR, self :: PARAM_MENTOR_ID => $mentor_rel_user_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerMentorViewerComponent :: TAB_USERS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerMentorRelUserSelected')));
        }
    }
}
?>