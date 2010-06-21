<?php
/**
 * $Id: unsubscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.agreement_manager.component
 */

class InternshipOrganizerAgreementManagerUnsubscriberComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
      $user = $this->get_user();
        
        $ids = Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_REL_LOCATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $agreementrellocation_ids = explode('|', $id);
                $agreementrellocation = $this->retrieve_agreement_rel_location($agreementrellocation_ids[1], $agreementrellocation_ids[0]);
                
                if (! isset($agreementrellocation))
                    continue;
                
                if ($agreementrellocation_ids[0] == $agreementrellocation->get_agreement_id())
                {
                    if (! $agreementrellocation->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
//                        Events :: trigger_event('unsubscribe_location', 'agreement', array('target_agreement_id' => $agreementrellocation->get_agreement_id(), 'target_agreement_id' => $agreementrellocation->get_location_id(), 'action_location_id' => $user->get_id()));
                    }
                }
                else
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerAgreementRelLocationNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerAgreementRelLocationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerAgreementRelLocationDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerAgreementRelLocationsDeleted';
                }
            }
          
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreementrellocation_ids[0]));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerAgreementRelLocationSelected')));
        }
    }
}
?>