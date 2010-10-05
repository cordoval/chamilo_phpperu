<?php
/**
 * $Id: survey_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component
 */
require_once dirname(__FILE__) . '/../survey_display.class.php';
require_once dirname(__FILE__) . '/viewer/survey_viewer_wizard.class.php';

class SurveyDisplaySurveyViewerComponent extends SurveyDisplay
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $wizard = new SurveyViewerWizard($this);
        return $wizard->run();
    }

    function started()
    {
        $this->get_parent()->started();
    }

    function finish()
    {
        $this->get_parent()->finish();
    }

    function save_answer($question_id, $answer, $context_path)
    {
        $this->get_parent()->save_answer($question_id, $answer, $context_path);
    }

    function get_answer($complex_question_id, $context_path)
    {
        return $this->get_parent()->get_answer($complex_question_id, $context_path);
    }

}
?>