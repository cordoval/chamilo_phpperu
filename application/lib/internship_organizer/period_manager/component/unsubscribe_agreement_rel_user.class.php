<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/agreement_viewer.class.php';

class InternshipOrganizerPeriodManagerUnsubscribeAgreementRelUserComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(InternshipOrganizerPeriodManager :: PARAM_USER_ID);
              
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
            
            switch ($agreement_rel_user_ids[2]) {
            	case InternshipOrganizerUserType:: COORDINATOR  :
            	$tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COORDINATOR;
            	break;
            	case InternshipOrganizerUserType:: COACH  :
            	$tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COACH;
            	break;
            	
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_AGREEMENT,InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID => $agreement_rel_user_ids[0], DynamicTabsRenderer::PARAM_SELECTED_TAB => $tab ));
                    }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerAgreementRelUserSelected')));
        }
    }
}
?>