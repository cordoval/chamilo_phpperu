<?php

class TestcaseManagerChangerComponent extends TestcaseManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
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
                $survey_publication = $this->get_survey_manager()->retrieve_survey_publication($id);
                
                if (! $survey_publication->is_visible_for_target_user($this->get_user()))
                {
                    $failures ++;
                }
                else
                {
                    
                    $survey_publication->set_test(0);
                    $users = $survey_publication->get_target_users();
                    $groups = $survey_publication->get_target_groups();
                	if ($survey_publication->create())
                    {
                       	$survey_publication->set_target_users($users);
                       	$survey_publication->set_target_groups($groups);
                       	$survey_publication->update();
                    	$survey_publication = $this->get_survey_manager()->retrieve_survey_publication($id);
                        if (! $survey_publication->delete())
                        {
                            $failures ++;
                        }
                    }else{
                    	$failures ++;
                    }
                }
                            
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('TestCaseNotChangedToProduction');
                }
                else
                {
                    $message = Translation :: get('TestCasesNotChangedToProduction');
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('TestCaseChangedToProduction');
                }
                else
                {
                    $message = Translation :: get('TestCasesChangedToProduction');
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
    
    }
}
?>