<?php
/**
 * $Id: editor.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerEditorComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('category general');
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => InternshipPlannerCategoryManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('InternshipPlannerCategory')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('InternshipPlannerCategoryList')));
        
        $id = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID);
        if ($id)
        {
            $category = $this->retrieve_category($id);
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID))), $category->get_name()));
            $trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $id)), Translation :: get('InternshipPlannerCategoryUpdate')));
            
            if (! $this->get_user()->is_platform_admin())
            {
                $this->display_header($trail, false);
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }
            
            $form = new InternshipPlannerCategoryForm(InternshipPlannerCategoryForm :: TYPE_EDIT, $category, $this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $id)), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_category();
                $category = $form->get_category();
                $this->redirect(Translation :: get($success ? 'InternshipPlannerCategoryUpdated' : 'InternshipPlannerCategoryNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
            }
            else
            {
                $this->display_header($trail, false);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerCategorySelected')));
        }
    }
}
?>