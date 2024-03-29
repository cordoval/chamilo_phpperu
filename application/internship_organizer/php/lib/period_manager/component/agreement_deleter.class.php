<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/viewer.class.php';

class InternshipOrganizerPeriodManagerAgreementDeleterComponent extends InternshipOrganizerPeriodManager
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
                
                $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($id);
                $period_id = $agreement->get_period_id();
             
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $id, InternshipOrganizerRights :: TYPE_AGREEMENT))
                {
                    
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
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerAgreementsSelected')));
        }
    }
}
?>