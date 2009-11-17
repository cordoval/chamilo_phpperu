<?php
/**
 * $Id: maintenance_wizard_page.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */


/**
 * This abstract class defines a page which is used in a maintenance wizard.
 */
abstract class MaintenanceWizardPage extends FormValidatorPage
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param string $name A unique name of this page in the wizard
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function MaintenanceWizardPage($name, $parent)
    {
        $this->parent = $parent;
        parent :: __construct($name, 'post');
        $this->updateAttributes(array('action' => $parent->get_url()));
    }

    /**
     * Returns the repository tool in which this wizard runs
     * @return Tool
     */
    function get_parent()
    {
        return $this->parent;
    }
}
?>