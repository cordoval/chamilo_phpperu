<?php
/**
 * $Id: migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 * 
 * This abstract class defines a page which is used in a migration wizard.
 */
abstract class MigrationWizardPage extends FormValidatorPage
{
    /**
     * The MigrationManager component in which the wizard runs.
     */
    private $parent;
    private $name;

    /**
     * Constructor
     * @param string $name A unique name of this page in the wizard
     * @param MigrationManagerComponent $parent The MigrationManager component
     * in which the wizard runs.
     */
    public function MigrationWizardPage($parent, $name)
    {
        parent :: __construct($name, 'post');
        
    	$this->parent = $parent;
        $this->name = $name;
    }

	function get_page_html()
	{
		
	}
    
    /**
     * Returns the MigrationManager component in which this wizard runs
     * @return MigrationManager
     */
    function get_parent()
    {
        return $this->parent;
    }
    
	function get_name()
    {
        return $this->name;
    }
    
    abstract function display_page_info();
    abstract function display_next_page_info();

}

?>