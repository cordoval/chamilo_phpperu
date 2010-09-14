<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/browser.class.php';

class InternshipOrganizerPeriodManagerUnsubscribeCategoryComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_REL_CATEGORY_ID);
        
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
                $location_id = InternshipOrganizerRights :: get_location_id_by_identifier_from_internship_organizers_subtree($period_id, InternshipOrganizerRights :: TYPE_PERIOD);
                
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: UNSUBSCRIBE_CATEGORY_RIGHT, $location_id, InternshipOrganizerRights :: TYPE_PERIOD))
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
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $category_rel_period_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_CATEGORIES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategoryRelPeriodSelected')));
        }
    }
}
?>