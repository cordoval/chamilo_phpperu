<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/browser.class.php';

class InternshipOrganizerCategoryManagerTruncaterComponent extends InternshipOrganizerCategoryManager
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
        
        $user = $this->get_user();
        
        $ids = Request :: get(self :: PARAM_CATEGORY_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $category = $this->retrieve_category($id);
                $parent_id = $category->get_parent_id();
                if (! $category->truncate())
                {
                    $failures ++;
                }
                //                else
            //                {
            //                    Event :: trigger('empty', 'category', array('target_category_id' => $category->get_id(), 'action_user_id' => $user->get_id()));
            //                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerCategoryNotEmptied';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoriesNotEmptied';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerCategoryEmptied';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoriesEmptied';
                }
            
            }
            
            if (count($ids) == 1)
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => $ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerCategoryManagerBrowserComponent :: TAB_LOCATIONS));
            else
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => $parent_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerCategoryManagerBrowserComponent :: TAB_SUB_CATEGORIES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategoriesSelected')));
        }
    }
}
?>