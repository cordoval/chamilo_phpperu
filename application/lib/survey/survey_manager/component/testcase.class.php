<?php

class SurveyManagerTestcaseComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
    	if (! SurveyRights :: is_allowed(SurveyRights :: VIEW_RIGHT, 'testcase_browser', 'sts_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
    	
    	$testcase_manager = new TestcaseManager($this);
        $testcase_manager->run();
    }
}
?>