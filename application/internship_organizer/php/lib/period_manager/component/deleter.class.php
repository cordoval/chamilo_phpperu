<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;
use common\libraries\EqualityCondition;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/browser.class.php';

class InternshipOrganizerPeriodManagerDeleterComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
              
        $ids = Request :: get(self :: PARAM_PERIOD_ID);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $period = $this->retrieve_period($id);
                $parent_id = $period->get_parent_id();
                
                $status = true;
                //if period has sub_periods, it isn't aloud to be deleted
                

                if ($period->has_children())
                {
                    $status = false;
                    $message = 'PeriodNotDeleted-HasSubPeriods';
                }
                
                $period_id = $period->get_id();
                
                //if there are still agreements attached, it isn't aloud to be deleted
                $condition = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_PERIOD_ID, $period_id);
                
                if (InternshipOrganizerDataManager :: get_instance()->count_agreements($condition) > 0)
                {
                    $status = false;
                    $message = 'PeriodNotDeleted-HasAgreements';
                }
                
                if ($status)
                {
                    if (! $period->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        //                    Event :: trigger('delete', 'period', array('target_period_id' => $period->get_id(), 'action_user_id' => $user->get_id()));
                    }
                    
                    if ($failures)
                    {
                        if (count($ids) == 1)
                        {
                            $message = 'SelectedInternshipOrganizerPeriodNotDeleted';
                        }
                        else
                        {
                            $message = 'SelectedInternshipOrganizerPeriodsNotDeleted';
                        }
                    }
                    else
                    {
                        if (count($ids) == 1)
                        {
                            $message = 'SelectedInternshipOrganizerPeriodDeleted';
                        }
                        else
                        {
                            $message = 'SelectedInternshipOrganizerPeriodsDeleted';
                        }
                    }
                }
            
            }
            
            $this->redirect(Translation :: get($message), ! $status, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $parent_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_SUBPERIODS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerPeriodSelected')));
        }
    }
}
?>