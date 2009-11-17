<?php
/**
 * $Id: assessment_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
require_once dirname(__FILE__) . '/../assessment_display.class.php';
require_once dirname(__FILE__) . '/../assessment_display_component.class.php';
require_once dirname(__FILE__) . '/viewer/assessment_viewer_wizard.class.php';

class AssessmentDisplayAssessmentViewerComponent extends AssessmentDisplayComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $wizard = new AssessmentViewerWizard($this, $this->get_root_lo());
        $wizard->run();
    }
}
?>