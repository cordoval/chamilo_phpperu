<?php
/**
 * $Id: unsubscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerUnsubscriberComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        
        if (! $user->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => InternshipPlannerCategoryManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('InternshipPlannerCategory')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('InternshipPlannerCategoryList')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UnsubscribeFromInternshipPlannerCategory')));
            $trail->add_help('category unsubscribe users');
            
            $this->display_header($trail);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $ids = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_REL_LOCATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $categoryreluser_ids = explode('|', $id);
                $categoryreluser = $this->retrieve_category_rel_user($categoryreluser_ids[1], $categoryreluser_ids[0]);
                
                if (! isset($categoryreluser))
                    continue;
                
                if ($categoryreluser_ids[0] == $categoryreluser->get_category_id())
                {
                    if (! $categoryreluser->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        Events :: trigger_event('unsubscribe_user', 'category', array('target_category_id' => $categoryreluser->get_category_id(), 'target_user_id' => $categoryreluser->get_user_id(), 'action_user_id' => $user->get_id()));
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
                    $message = 'SelectedInternshipPlannerCategoryRelLocationNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipPlannerCategoryRelLocationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipPlannerCategoryRelLocationDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipPlannerCategoryRelLocationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $categoryreluser_ids[0]));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerCategoryRelLocationSelected')));
        }
    }
}
?>