<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/category_form.class.php';

class InternshipOrganizerCategoryManagerEditorComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('category general');
        $trail->add(new Breadcrumb($this->get_browse_categories_url(), Translation :: get('BrowseInternshipOrganizerCategories')));
        
        $id = Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID);
        if ($id)
        {
            $category = $this->retrieve_category($id);
            $trail->add(new Breadcrumb($this->get_category_viewing_url($category), $category->get_name()));
            $trail->add(new Breadcrumb($this->get_category_editing_url($category), Translation :: get('UpdateInternshipOrganizerCategory').' '.$category->get_name()));
                                  
            $form = new InternshipOrganizerCategoryForm(InternshipOrganizerCategoryForm :: TYPE_EDIT, $category, $this->get_category_editing_url($category), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_category();
                $category = $form->get_category();
                $this->redirect(Translation :: get($success ? 'InternshipOrganizerCategoryUpdated' : 'InternshipOrganizerCategoryNotUpdated'), ($success ? false : true), array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
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
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategorySelected')));
        }
    }
}
?>