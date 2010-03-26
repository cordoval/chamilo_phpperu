<?php
/**
 * $Id: creator.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerCreatorComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => InternshipPlannerCategoryManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('InternshipPlannerCategory')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipPlannerCategory')));
        $trail->add_help('category general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $category = new InternshipPlannerCategory();
        $category->set_parent(Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID));
        $form = new InternshipPlannerCategoryForm(InternshipPlannerCategoryForm :: TYPE_CREATE, $category, $this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_category();
            if ($success)
            {
                $category = $form->get_category();
                $this->redirect(Translation :: get('InternshipPlannerCategoryCreated'), (false), array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipPlannerCategoryNotCreated'), (true), array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES));
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>