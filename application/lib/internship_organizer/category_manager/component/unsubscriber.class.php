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
        $user = $this->get_user();
        
        if (! $user->is_platform_admin())
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerCategoryManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('InternshipOrganizerCategory')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('BrowseInternshipOrganizerCategories')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UnsubscribeFromInternshipOrganizerCategory')));
            $trail->add_help('category unsubscribe users');
            
            $this->display_header($trail);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
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
//                $categoryreluser_ids = explode('|', $id);
//                $categoryreluser = $this->retrieve_category_rel_user($categoryreluser_ids[1], $categoryreluser_ids[0]);

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
//                        Events :: trigger_event('unsubscribe', 'category', array('target_category_id' => $categoryrellocation->get_category_id(), 'target_location_id' => $categoryrellocation->get_location_id(), 'action_location_id' => $location->get_location_id()));
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
            
//            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $categoryreluser_ids[0]));
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $categoryrellocation_ids[0]));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategoryRelLocationSelected')));
        }
    }
}
?>