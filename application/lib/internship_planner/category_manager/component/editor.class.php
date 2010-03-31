<?php

require_once Path :: get_application_path() . 'lib/internship_planner/forms/category_form.class.php';

class InternshipPlannerCategoryManagerEditorComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('category general');
        $trail->add(new Breadcrumb($this->get_browse_categories_url(), Translation :: get('BrowseCategories')));
        
        $id = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID);
        if ($id)
        {
            $category = $this->retrieve_category($id);
            $trail->add(new Breadcrumb($this->get_category_viewing_url($category), $category->get_name()));
            $trail->add(new Breadcrumb($this->get_category_editing_url($category), Translation :: get('UpdateCategory').' '.$category->get_name()));
                                  
            $form = new InternshipPlannerCategoryForm(InternshipPlannerCategoryForm :: TYPE_EDIT, $category, $this->get_category_editing_url($category), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_category();
                $category = $form->get_category();
                $this->redirect(Translation :: get($success ? 'CategoryUpdated' : 'CategoryNotUpdated'), ($success ? false : true), array(InternshipPlannerCategoryManager :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
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
            $this->display_error_page(htmlentities(Translation :: get('NoCategorySelected')));
        }
    }
}
?>