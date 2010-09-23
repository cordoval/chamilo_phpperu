<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/category_form.class.php';

class InternshipOrganizerCategoryManagerEditorComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
       
        $id = Request :: get(self :: PARAM_CATEGORY_ID);
        if ($id)
        {
            $category = $this->retrieve_category($id);
            
            $form = new InternshipOrganizerCategoryForm(InternshipOrganizerCategoryForm :: TYPE_EDIT, $category, $this->get_category_editing_url($category), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_category();
                $category = $form->get_category();
                $this->redirect(Translation :: get($success ? 'InternshipOrganizerCategoryUpdated' : 'InternshipOrganizerCategoryNotUpdated'), ($success ? false : true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => $category->get_id()));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategorySelected')));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => Request :: get(self :: PARAM_CATEGORY_ID))), Translation :: get('BrowseInternshipOrganizerCategories')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CATEGORY_ID);
    }
}
?>