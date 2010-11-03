<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/browser.class.php';

class InternshipOrganizerPeriodManagerUnsubscribeCategoryComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(self :: PARAM_PERIOD_REL_CATEGORY_ID);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $category_rel_period_ids = explode('|', $id);
                
                $period_id = $category_rel_period_ids[1];
                
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_CATEGORY_RIGHT, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
                {
                    
                    $category_rel_period = InternshipOrganizerDataManager :: get_instance()->retrieve_category_rel_period($category_rel_period_ids[0], $category_rel_period_ids[1]);
                    
                    if (! $category_rel_period->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        
                    //                    Event :: trigger('delete', 'period', array('target_period_id' => $period->get_id(), 'action_user_id' => $user->get_id()));
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerCategoryRelPeriodNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoryRelPeriodsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerCategoryRelPeriodDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoryRelPeriodsDeleted';
                }
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $category_rel_period_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_CATEGORIES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategoryRelPeriodSelected')));
        }
    }
}
?>