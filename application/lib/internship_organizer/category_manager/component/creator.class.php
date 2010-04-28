<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/category_form.class.php';


class InternshipOrganizerCategoryManagerCreatorComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_categories_url(), Translation :: get('BrowseCategories')));
        
        $trail->add(new Breadcrumb($this->get_category_create_url, Translation :: get('CreateCategory')));
        $trail->add_help('category general');
             
        $category = new InternshipOrganizerCategory();
        $category->set_parent_id(Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID));
        $form = new InternshipOrganizerCategoryForm(InternshipOrganizerCategoryForm :: TYPE_CREATE, $category, $this->get_url(array(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_category();
            if ($success)
            {
                $category = $form->get_category();
                $this->redirect(Translation :: get('InternshipOrganizerCategoryCreated'), (false), array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerCategoryNotCreated'), (true), array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_BROWSE_CATEGORIES));
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