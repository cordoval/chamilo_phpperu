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
        $wizard = new SurveyViewerWizard($this, $this->get_root_content_object(), $this->get_parent()->get_template_id());
        return $wizard->run();
    }
}
?>