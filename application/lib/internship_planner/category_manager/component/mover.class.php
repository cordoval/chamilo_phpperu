<?php
/**
 * $Id: mover.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerMoverComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => InternshipPlannerCategoryManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('InternshipPlannerCategory')));
        $trail->add_help('category general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $category = $this->retrieve_categories(new EqualityCondition(InternshipPlannerCategory :: PROPERTY_ID, Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID)))->next_result();
        
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID))), $category->get_name()));
        
        $form = new InternshipPlannerCategoryMoveForm($category, $this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->move_category();
            $parent = $form->get_new_parent();
            $this->redirect($success ? Translation :: get('InternshipPlannerCategoryMoved') : Translation :: get('InternshipPlannerCategoryNotMoved'), $success ? (false) : true, array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $parent));
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Move')));
            $this->display_header($trail);
            echo Translation :: get('InternshipPlannerCategory') . ': ' . $category->get_name();
            $form->display();
            $this->display_footer();
        }
    }
}
?>