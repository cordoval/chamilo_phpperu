<?php
namespace application\internship_organizer;

use common\libraries\Translation;
use common\libraries\WebApplication;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/browser.class.php';

class InternshipOrganizerPeriodManagerUnsubscribeGroupComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(self :: PARAM_PERIOD_REL_GROUP_ID);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $period_rel_group_ids = explode('|', $id);
                
                $period_id = $period_rel_group_ids[0];
                
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_GROUP_RIGHT, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
                {
                    
                    $period_rel_group = InternshipOrganizerDataManager :: get_instance()->retrieve_period_rel_group($period_rel_group_ids[0], $period_rel_group_ids[1], $period_rel_group_ids[2]);
                    
                    if (! $period_rel_group->delete())
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
                    $message = 'SelectedInternshipOrganizerPeriodRelGroupNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerPeriodRelGroupsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerPeriodRelGroupDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerPeriodRelGroupsDeleted';
                }
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $period_rel_group_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_GROUPS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerPeriodRelGroupSelected')));
        }
    }
}
?>