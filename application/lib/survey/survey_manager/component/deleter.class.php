<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';


class SurveyManagerDeleterComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $testcase = false;
        $action = Request :: get(SurveyManager :: PARAM_ACTION);
        if ($action === SurveyManager :: ACTION_TESTCASE)
        {
            $testcase = true;
        }
               
        $ids = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                         	
            	$survey_publication = $this->retrieve_survey_publication($id);
                
                if (! $survey_publication->is_visible_for_target_user($this->get_user()))
                {
                    $failures ++;
                }
                else
                {
                    if (! $survey_publication->delete())
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyPublicationsDeleted';
                }
            }
            
            if ($testcase)
            {
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }
            else
            {
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyPublicationsSelected')));
        }
    }
}
?>