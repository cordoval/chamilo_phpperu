<?php

class InternshipOrganizerPeriodManagerDeleterComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        
        $ids = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        
        $failures = 0;
        $parent_id = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $period = $this->retrieve_period($id);
                $parent_id = $this->get_parent();
                if (! $period->delete())
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
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerPeriodSelected')));
        }
    }
}
?>