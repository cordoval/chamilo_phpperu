<?php

class InternshipOrganizerRegionManagerDeleterComponent extends InternshipOrganizerRegionManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
    	if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
            
        $ids = Request :: get(self :: PARAM_REGION_ID);
        
        $failures = 0;
        $parent_id = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $region = $this->retrieve_region($id);
               
                if (! $region->delete())
                {
                    $failures ++;
                }
                else
                {
                    //                    Event :: trigger('delete', 'region', array('target_region_id' => $region->get_id(), 'action_user_id' => $user->get_id()));
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerRegionNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerRegionsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerRegionDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerRegionsDeleted';
                }
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS, self :: PARAM_REGION_ID => $region->get_parent_id()));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerRegionSelected')));
        }
    }
}
?>