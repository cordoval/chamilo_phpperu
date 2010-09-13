<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/category_form.class.php';

class InternshipOrganizerCategoryManagerCreatorComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
    	
    	if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
    	
    	$trail = BreadcrumbTrail :: get_instance();
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