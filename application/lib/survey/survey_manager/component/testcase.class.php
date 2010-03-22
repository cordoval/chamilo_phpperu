<?php

class SurveyManagerTestcaseComponent extends SurveyManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $testcase_manager = new TestcaseManager($this->get_parent());
        $testcase_manager->run();
    }
}
?>