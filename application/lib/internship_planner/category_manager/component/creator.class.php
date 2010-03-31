<?php
require_once Path :: get_application_path() . 'lib/internship_planner/forms/category_form.class.php';


class InternshipPlannerCategoryManagerCreatorComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
         $trail->add(new Breadcrumb($this->get_browse_categories_url(), Translation :: get('BrowseInternshipPlannerCategories')));
        
        $trail->add(new Breadcrumb($this->get_category_create_url, Translation :: get('CreateInternshipPlannerCategory')));
        $trail->add_help('category general');
             
        $category = new InternshipPlannerCategory();
        $category->set_parent_id(Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID));
        $form = new InternshipPlannerCategoryForm(InternshipPlannerCategoryForm :: TYPE_CREATE, $category, $this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_category();
            if ($success)
            {
                $category = $form->get_category();
                $this->redirect(Translation :: get('InternshipPlannerCategoryCreated'), (false), array(InternshipPlannerCategoryManager :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipPlannerCategoryNotCreated'), (true), array(InternshipPlannerCategoryManager :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES));
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