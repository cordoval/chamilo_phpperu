<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/category_form.class.php';

class InternshipOrganizerCategoryManagerCreatorComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $category = new InternshipOrganizerCategory();
        $category->set_parent_id(Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID));
        $form = new InternshipOrganizerCategoryForm(InternshipOrganizerCategoryForm :: TYPE_CREATE, $category, $this->get_url(array(self :: PARAM_CATEGORY_ID => Request :: get(self :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_category();
            if ($success)
            {
                $category = $form->get_category();
                $this->redirect(Translation :: get('InternshipOrganizerCategoryCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => $category->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerCategoryNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => Request :: get(self :: PARAM_CATEGORY_ID)));
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
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