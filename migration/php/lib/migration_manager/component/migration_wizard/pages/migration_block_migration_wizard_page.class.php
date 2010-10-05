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
    
	private $block;
	
	function MigrationBlockMigrationWizardPage($parent, $name, $block)
	{
		parent :: __construct($parent, $name);
		$this->block = $block;
	}
	
	function get_block()
	{
		return $this->block;
	}
	
	function display_page_info()
 	{
 		echo Translation :: get(Utilities :: underscores_to_camelcase($this->get_block()) . 'MigrationBlockInfo');
 	}  

 	function display_next_page_info()
 	{
 		$block = $this->get_parent()->get_next_block($this->get_block());
 		if($block)
 		{
 			echo Translation :: get(Utilities :: underscores_to_camelcase($block) . 'MigrationBlockInfoNext');
 		}
 		else
 		{
 			echo Translation :: get('MigrationFinished');
 		}
 	}  
 	
    /**
     * Build the form
     */
    function buildForm()
    {
    	$block = $this->get_parent()->get_next_block($this->get_block());
 		if($block)
 		{
    		$button = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next') . ' >>', array('class' => 'normal next'));
        	$this->addElement($button);
 		}
    }
    
	function display_page_html()
	{ 
		$migration_block = MigrationBlock :: factory($this->get_parent()->get_platform(), $this->get_block());
		$migration_block->migrate();

		echo '<div class="migrate_info">';
		echo '<div class="title">' . Translation :: get('Migrate' . Utilities :: underscores_to_camelcase($this->get_block())) . '</div>';
		echo $migration_block->render_message();
		echo '</div>';
	}

}
?>