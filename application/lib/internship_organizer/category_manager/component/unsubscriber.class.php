<?php
/**
 * $Id: unsubscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipOrganizerCategoryManagerUnsubscriberComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $user = $this->get_user();
    
        $ids = Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_REL_LOCATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $categoryrellocation_ids = explode('|', $id);
                $categoryrellocation = $this->retrieve_category_rel_location($categoryrellocation_ids[1], $categoryrellocation_ids[0]);
                
                if (! isset($categoryrellocation))
                    continue;
                
                if ($categoryrellocation_ids[0] == $categoryrellocation->get_category_id())
                {
                    if (! $categoryrellocation->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        //                        Event :: trigger('unsubscribe', 'category', array('target_category_id' => $categoryrellocation->get_category_id(), 'target_location_id' => $categoryrellocation->get_location_id(), 'action_location_id' => $location->get_location_id()));
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
                    $message = 'SelectedInternshipOrganizerCategoryRelLocationNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoryRelLocationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerCategoryRelLocationDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoryRelLocationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_BROWSE_CATEGORIES, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $categoryrellocation_ids[0]));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategoryRelLocationSelected')));
        }
    }
}
?>