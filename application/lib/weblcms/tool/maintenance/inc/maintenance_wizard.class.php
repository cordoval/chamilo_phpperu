<?php
/**
 * $Id: maintenance_wizard.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';
require_once dirname(__FILE__) . '/wizard/publication_selection_maintenance_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/action_selection_maintenance_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course_selection_maintenance_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/confirmation_maintenance_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/maintenance_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/maintenance_wizard_display.class.php';
/**
 * A wizard which guides the user to several steps to complete a maintenance
 * action on a course.
 */
class MaintenanceWizard extends HTML_QuickForm_Controller
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
    function MaintenanceWizard($parent)
    {
        $this->parent = $parent;
        parent :: HTML_QuickForm_Controller('MaintenanceWizard', true);
        $this->addPage(new ActionSelectionMaintenanceWizardPage('action_selection', $this->parent));
        $this->addAction('process', new MaintenanceWizardProcess($this->parent));
        $this->addAction('display', new MaintenanceWizardDisplay($this->parent));
        $values = $this->exportValues();
        $action = null;
        $action = isset($values['action']) ? $values['action'] : null;
        $action = is_null($action) ? $_POST['action'] : $action;
        switch ($action)
        {
            case ActionSelectionMaintenanceWizardPage :: ACTION_EMPTY :
                $this->addPage(new PublicationSelectionMaintenanceWizardPage('publication_selection', $this->parent));
                $this->addPage(new ConfirmationMaintenanceWizardPage('confirmation', $this->parent, Translation :: get('EmptyConfirmationQuestion')));
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_COPY :
                $this->addPage(new PublicationSelectionMaintenanceWizardPage('publication_selection', $this->parent));
                $this->addPage(new CourseSelectionMaintenanceWizardPage('course_selection', $this->parent));
                $this->addPage(new ConfirmationMaintenanceWizardPage('confirmation', $this->parent, Translation :: get('CopyConfirmationQuestion')));
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_BACKUP :
                $this->addPage(new PublicationSelectionMaintenanceWizardPage('publication_selection', $this->parent));
                $this->addPage(new ConfirmationMaintenanceWizardPage('confirmation', $this->parent, Translation :: get('BackupConfirmationQuestion')));
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_DELETE :
                $this->addPage(new ConfirmationMaintenanceWizardPage('confirmation', $this->parent, Translation :: get('DeleteConfirmationQuestion')));
                break;
        }
    }
}
?>