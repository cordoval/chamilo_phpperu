<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/category_move_form.class.php';

class InternshipOrganizerCategoryManagerMoverComponent extends InternshipOrganizerCategoryManager
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
               
        $category = $this->retrieve_categories(new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_ID, Request :: get(self :: PARAM_CATEGORY_ID)))->next_result();
        
        $form = new InternshipOrganizerCategoryMoveForm($category, $this->get_url(array(self :: PARAM_CATEGORY_ID => Request :: get(self :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->move_category();
            $parent = $form->get_new_parent();
            $this->redirect($success ? Translation :: get('InternshipOrganizerCategoryMoved') : Translation :: get('InternshipOrganizerCategoryNotMoved'), $success ? (false) : true, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => $parent));
        }
        else
        {
            $this->display_header();
            echo Translation :: get('Category') . ': ' . $category->get_name();
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