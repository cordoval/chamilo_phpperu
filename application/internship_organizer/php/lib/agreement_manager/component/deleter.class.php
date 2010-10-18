<?php

require_once require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';
require_once require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/browser.class.php';


class InternshipOrganizerAgreementManagerDeleterComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = $_GET[self :: PARAM_AGREEMENT_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $id, InternshipOrganizerRights :: TYPE_AGREEMENT))
                {
                    $agreement = $this->retrieve_agreement($id);
                    
                    $status = $agreement->get_status();
                    
                    if (! $agreement->delete())
                    {
                        $failures ++;
                    }
                }
            
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerAgreementNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerAgreementsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerAgreementDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerAgreementsDeleted';
                }
            }
            if ($status == InternshipOrganizerAgreement :: STATUS_ADD_LOCATION)
            {
            	$tab = InternshipOrganizerAgreementManagerBrowserComponent :: TAB_ADD_LOCATION;
            }
            if ($status == InternshipOrganizerAgreement :: STATUS_APPROVED)
            {
            	$tab = InternshipOrganizerAgreementManagerBrowserComponent :: TAB_APPROVED;
            }
            if ($status == InternshipOrganizerAgreement :: STATUS_TO_APPROVE)
            {
            	$tab = InternshipOrganizerAgreementManagerBrowserComponent :: TAB_TO_APPROVE;
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT, DynamicTabsRenderer::PARAM_SELECTED_TAB =>$tab));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerAgreementsSelected')));
        }
    }
}
?>