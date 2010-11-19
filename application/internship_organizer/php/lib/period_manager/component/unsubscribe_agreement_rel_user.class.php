<?php
namespace application\internship_organizer;

use common\libraries\Translation;
use common\libraries\WebApplication;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/agreement_viewer.class.php';

class InternshipOrganizerPeriodManagerUnsubscribeAgreementRelUserComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(self :: PARAM_USER_ID);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $agreement_rel_user_ids = explode('|', $id);
                
                $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($agreement_rel_user_ids[0]);
                $period_id = $agreement->get_period_id();
                
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_AGREEMENT_USER_RIGHT, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
                {
                    
                    $agreement_rel_user = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_user($agreement_rel_user_ids[0], $agreement_rel_user_ids[1], $agreement_rel_user_ids[2]);
                    
                    if (! $agreement_rel_user->delete())
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
                    $message = 'SelectedInternshipOrganizerAgreementRelUserNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerAgreementRelUsersNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerAgreementRelUserDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerAgreementRelUsersDeleted';
                }
            }
            
            switch ($agreement_rel_user_ids[2])
            {
                case InternshipOrganizerUserType :: COORDINATOR :
                    $tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COORDINATOR;
                    break;
                case InternshipOrganizerUserType :: COACH :
                    $tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COACH;
                    break;
            
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_rel_user_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerAgreementRelUserSelected')));
        }
    }
}
?>