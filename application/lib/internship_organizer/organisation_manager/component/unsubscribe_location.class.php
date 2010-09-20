<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/mentor_viewer.class.php';


class InternshipOrganizerOrganisationManagerUnsubscribeLocationComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
         $ids = Request :: get(self :: PARAM_MENTOR_REL_LOCATION_ID);
      
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $mentor_rel_location_ids = explode('|', $id);
                $mentor_rel_location = InternshipOrganizerDataManager::get_instance()->retrieve_mentor_rel_location($mentor_rel_location_ids[0], $mentor_rel_location_ids[1]);
                
                if (! isset($mentor_rel_location))
                    continue;
                
                if ($mentor_rel_location_ids[0] == $mentor_rel_location->get_mentor_id())
                {
                    
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT,InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
                    {
                        if (! $mentor_rel_location->delete())
                        {
                            $failures ++;
                        }
                        else
                        {
                            //                        Event :: trigger('unsubscribe_user', 'organisation', array('target_organisation_id' => $mentor_rel_location->get_organisation_id(), 'target_organisation_id' => $mentor_rel_location->get_user_id(), 'action_user_id' => $user->get_id()));
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
                    $message = 'SelectedInternshipOrganizerMentorRelLocationNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerMentorRelLocationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerMentorRelLocationDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerMentorRelLocationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_MENTOR, self :: PARAM_MENTOR_ID => $mentor_rel_location_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerMentorViewerComponent :: TAB_LOCATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerMentorRelLocationSelected')));
        }
    }
  
    
}
?>