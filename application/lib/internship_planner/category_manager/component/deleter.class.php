<?php
/**
 * $Id: deleter.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerDeleterComponent extends InternshipPlannerCategoryManagerComponent
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
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('DeleteInternshipPlannerCategory')));
            $trail->add_help('category general');
            
            $this->display_header($trail, false);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $ids = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $category = $this->retrieve_category($id);
                
                if (! $category->delete())
                {
                    $failures ++;
                }
                else
                {
                    Events :: trigger_event('delete', 'category', array('target_category_id' => $category->get_id(), 'action_user_id' => $user->get_id()));
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipPlannerCategoryDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipPlannerCategoryDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipPlannerCategorysDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipPlannerCategorysDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerCategorysSelected')));
        }
    }
}
?>