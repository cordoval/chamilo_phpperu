<?php
/**
 * $Id: build_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
/**
 * This abstract class defines a page which is used in a maintenance wizard.
 */
abstract class BuildWizardPage extends HTML_QuickForm_Page
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;
    private $wizard;

    /**
     * Constructor
     * @param string $name A unique name of this page in the wizard
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function BuildWizardPage($name, $parent, $wizard = null)
    {
        $this->parent = $parent;
        $this->wizard = $wizard;
        parent :: HTML_QuickForm_Page($name, 'post');
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

    function get_wizard()
    {
        return $this->wizard;
    }
}
?>