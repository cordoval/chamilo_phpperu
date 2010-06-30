<?php
/**
 * $Id: subscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipOrganizerCategoryManagerSubscriberComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        $failures = 0;

        $selected_ids =Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_REL_LOCATION_ID);
       
        if (! empty($selected_ids))
        {
            if (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            
            foreach ($selected_ids as $selected_id)
            {
                
            	$ids = explode('|', $selected_id);
                $location_id= $ids[1];
                $category_id = $ids[0];
            	
            	$existing_categoryrellocation = $this->retrieve_category_rel_location($location_id, $category_id);
                
                if (! $existing_categoryrellocation)
                {
                    $categoryrellocation = new InternshipOrganizerCategoryRelLocation();
                    $categoryrellocation->set_category_id($category_id);
                    $categoryrellocation->set_location_id($location_id);
                    
                    if (! $categoryrellocation->create())
                    {
                        $failures ++;
                    }
                    //                    else
                //                    {
                //                        Event :: trigger('subscribe_location', 'category', array('target_category_id' => $categoryrellocation->get_category_id(), 'target_location_id' => $categoryrellocation->get_location_id(), 'action_user_id' => $this->get_user()->get_id()));
                //                    }
                }
                else
                {
                    $contains_dupes = true;
                }
            }
            
            //$this->get_result( not good enough?
            if ($failures)
            {
                if (count($locations) == 1)
                {
                    $message = 'SelectedLocationNotAddedToInternshipOrganizerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedLocationsNotAddedToInternshipOrganizerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            else
            {
                if (count($locations) == 1)
                {
                    $message = 'SelectedLocationAddedToInternshipOrganizerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedLocationsAddedToInternshipOrganizerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $category_id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategoryRelLocationSelected')));
        }
    }
}
?>