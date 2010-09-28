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

    function get_invitee_id()
    {
        return $this->get_parent()->get_invitee_id();
    }

    function started($survey_id)
    {
        $this->get_parent()->started($survey_id);
    }

    function finish($survey_id)
    {
       $this->get_parent()->finish($survey_id);
    }

    function started_context($survey_id, $context_template, $context_id)
    {
        $this->get_parent()->started_context($survey_id, $context_template, $context_id);
    }

    function finish_context($survey, $template_id, $context_id)
    {
        $this->get_parent()->finish_context($survey, $template_id, $context_id);
    }

    function save_answer($question_id, $answer, $template_id, $context_id)
    {
       $this->get_parent()->save_answers($question_id, $answer, $template_id, $context_id);
    }

}
?>