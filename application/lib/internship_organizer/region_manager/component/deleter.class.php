<?php

class InternshipOrganizerRegionManagerDeleterComponent extends InternshipOrganizerRegionManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
             
        $ids = Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID);

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
                $parent_id = $this->get_parent();
                if (! $region->delete())
                {
                    $failures ++;
                }
                else
                {
//                    Events :: trigger_event('delete', 'region', array('target_region_id' => $region->get_id(), 'action_user_id' => $user->get_id()));
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
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => InternshipOrganizerRegionManager :: ACTION_BROWSE_REGIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerRegionSelected')));
        }
    }
}
?>