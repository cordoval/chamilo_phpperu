<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/category_move_form.class.php';

class InternshipOrganizerCategoryManagerMoverComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $trail->add_help('category general');
        
        $category = $this->retrieve_categories(new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_ID, Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID)))->next_result();
     
        $form = new InternshipOrganizerCategoryMoveForm($category, $this->get_url(array(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->move_category();
            $parent = $form->get_new_parent();
            $this->redirect($success ? Translation :: get('InternshipOrganizerCategoryMoved') : Translation :: get('InternshipOrganizerCategoryNotMoved'), $success ? (false) : true, array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_BROWSE_CATEGORIES, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $parent));
        }
        else
        {
            //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('InternshipOrganizerCategoryMove')));
            $this->display_header($trail);
            echo Translation :: get('Category') . ': ' . $category->get_name();
            $form->display();
            $this->display_footer();
        }
    }
}
?>