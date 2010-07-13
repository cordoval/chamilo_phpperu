<?php
/**
 * $Id: system_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';

/**
 * This form can be used to let the user select the settings
 * 
 * @author Sven Vanpoucke
 */
class MigrationBlockMigrationWizardPage extends MigrationWizardPage
{
    
	function display_page_info()
 	{
 		echo Translation :: get('');
 	}  

 	function display_next_page_info()
 	{
 		echo Translation :: get('');
 	}  
 	
    /**
     * Build the form
     */
    function buildForm()
    {
    	$button = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next') . ' >>', array('class' => 'normal next'));
        $this->addElement($button);
    }
    
	function get_page_html()
	{
		$migration_block = MigrationBlock :: factory($this->get_parent()->get_platform(), $this->get_name());
		$migration_block->migrate();

		echo $migration_block->get_messages_as_string();
	}

}
?>