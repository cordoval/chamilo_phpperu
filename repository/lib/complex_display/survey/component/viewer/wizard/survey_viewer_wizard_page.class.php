<?php
/**
 * $Id: survey_viewer_wizard_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard
 */

abstract class SurveyViewerWizardPage extends FormValidatorPage
{
    /**
     * The parent in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param string $name A unique name of this page in the wizard
     * @param Tool $parent The parent in which the wizard
     * runs.
     */
    public function SurveyViewerWizardPage($name, $parent)
    {
       	$this->parent = $parent;
        parent :: FormValidatorPage($name, 'post');
        $this->updateAttributes(array('action' => $parent->get_parent()->get_url()));
    }

    /**
     * Returns the parent in which this wizard runs
     * @return Component
     */
    function get_parent()
    {
        return $this->parent;
    }
}
?>