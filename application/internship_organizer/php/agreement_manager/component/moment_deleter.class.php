<?php

require_once Path :: get_application_path() . 'internship_organizer/php/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/agreement_manager/component/viewer.class.php';


class InternshipOrganizerAgreementManagerMomentDeleterComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[self :: PARAM_MOMENT_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $moment = $this->retrieve_moment($id);
            	$agreement_id = $moment->get_agreement_id();
            	if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $id, InternshipOrganizerRights :: TYPE_MOMENT))
                {
                    if (! $moment->delete())
                    {
                        $failures ++;
                    }
                }
            
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerMomentNotDeleted';
                }
                else
                {
                    $message = 'Selected{InternshipOrganizerMomentsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerMomentDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerMomentsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerMomentsSelected')));
        }
    }
}
?>