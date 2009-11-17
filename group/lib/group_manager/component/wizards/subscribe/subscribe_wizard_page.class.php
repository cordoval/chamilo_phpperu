<?php
/**
 * $Id: subscribe_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package groups.lib.group_manager.component.wizards.subscribe
 */
/**
 * This abstract class defines a page which is used in a maintenance wizard.
 */
abstract class SubscribeWizardPage extends HTML_QuickForm_Page
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
    public function SubscribeWizardPage($name, $parent, $wizard = null)
    {
        $this->registerElementType('element_finder', Path :: get_library_path() . 'html/formvalidator/Element/element_finder.php', 'HTML_QuickForm_element_finder');
        $this->parent = $parent;
        $this->wizard = $wizard;
        parent :: HTML_QuickForm_Page($name, 'post');
        $this->updateAttributes(array('action' => $parent->get_url(array(GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID)))));
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