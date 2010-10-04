<?php
/**
 * $Id: laika_wizard.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';
require_once dirname(__FILE__) . '/wizard/questions_laika_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/laika_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/laika_wizard_display.class.php';
/**
 * A wizard which guides the user to several steps to complete a maintenance
 * action on a course.
 */
class LaikaWizard extends HTML_QuickForm_Controller
{
    /**
     * The repository tool in which this wizard runs.
     */
    private $parent;

    /**
     * Creates a new MaintenanceWizard
     * @param Tool $parent The repository tool in which this wizard
     * runs.
     */
    function LaikaWizard($parent)
    {
        $this->parent = $parent;
        parent :: HTML_QuickForm_Controller('LaikaWizard', true);
        
        $values = $this->exportValues();
        
        $ldm = LaikaDataManager :: get_instance();
        $questions_count = $ldm->count_laika_questions();
        
        $i = 0;
        
        while ($i <= $questions_count)
        {
            $this->addPage(new QuestionsLaikaWizardPage('questions_' . $i, $this->parent, $i));
            $i += 10;
        }
        
        $this->addAction('process', new LaikaWizardProcess($this->parent));
        $this->addAction('display', new LaikaWizardDisplay($this->parent));
    }
}
?>