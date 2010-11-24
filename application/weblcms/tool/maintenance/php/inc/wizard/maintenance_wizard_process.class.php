<?php

namespace application\weblcms\tool\maintenance;

use Exception;
use HTML_QuickForm_Action;

/**
 * $Id: maintenance_wizard_process.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class MaintenanceWizardProcess extends HTML_QuickForm_Action
{

    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    function perform($page, $actionName)
    {
        $values = $page->controller->exportValues();
        //Todo: Split this up in several form-processing classes depending on selected action
        switch ($values['action'])
        {
            case ActionSelectionMaintenanceWizardPage :: ACTION_EMPTY :
                require_once dirname(__FILE__) . '/action/action_empty.class.php';
                ActionEmpty :: factory($this->get_parent())->perform($page, $actionName);
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_COPY :
                require_once dirname(__FILE__) . '/action/action_copy.class.php';
                ActionCopy :: factory($this->get_parent())->perform($page, $actionName);
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_BACKUP :
                require_once dirname(__FILE__) . '/action/action_backup.class.php';
                ActionBackup :: factory($this->get_parent())->perform($page, $actionName);
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_DELETE :
                require_once dirname(__FILE__) . '/action/action_delete.class.php';
                ActionDelete :: factory($this->get_parent())->perform($page, $actionName);
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_EXPORT_CP :
                require_once dirname(__FILE__) . '/action/action_export_cp.class.php';
                ActionExportCp :: factory($this->get_parent())->perform($page, $actionName);
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_IMPORT_CP :
                require_once dirname(__FILE__) . '/action/action_import_cp.class.php';
                ActionImportCp :: factory($this->get_parent())->perform($page, $actionName);
                break;

            default :
                throw new Exception('Case not implemented');
        }
    }

}

?>