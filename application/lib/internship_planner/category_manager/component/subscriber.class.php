<?php
/**
 * $Id: subscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerSubscriberComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        $category_id = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID);
        if (! $user->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => InternshipPlannerCategoryManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('InternshipPlannerCategory')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('InternshipPlannerCategoryList')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SubscribeToInternshipPlannerCategory')));
            $trail->add_help('category general');
            
            $this->display_header($trail);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $users = Request :: get(InternshipPlannerCategoryManager :: PARAM_LOCATION_ID);
        
        $failures = 0;
        
        if (! empty($users))
        {
            if (! is_array($users))
            {
                $users = array($users);
            }
            
            foreach ($users as $user)
            {
                $existing_categoryreluser = $this->retrieve_category_rel_user($user, $category_id);
                
                if (! is_null($existing_categoryreluser))
                {
                    $categoryreluser = new InternshipPlannerCategoryRelLocation();
                    $categoryreluser->set_category_id($category_id);
                    $categoryreluser->set_user_id($user);
                    
                    if (! $categoryreluser->create())
                    {
                        $failures ++;
                    }
                    else
                    {
                        Events :: trigger_event('subscribe_user', 'category', array('target_category_id' => $categoryreluser->get_category_id(), 'target_user_id' => $categoryreluser->get_user_id(), 'action_user_id' => $this->get_user()->get_id()));
                    }
                }
                else
                {
                    $contains_dupes = true;
                }
            }
            
            if ($failures)
            {
                if (count($users) == 1)
                {
                    $message = 'SelectedLocationNotAddedToInternshipPlannerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedLocationsNotAddedToInternshipPlannerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            else
            {
                if (count($users) == 1)
                {
                    $message = 'SelectedLocationAddedToInternshipPlannerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedLocationsAddedToInternshipPlannerCategory' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category_id));
            exit();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerCategoryRelLocationSelected')));
        }
    }
}
?>