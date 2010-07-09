<?php
/**
 * $Id: group_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.forms
 */

class MigrationForm extends FormValidator
{
    const PLATFORM = 'platform';
    const PLATFORM_PATH = 'platform_path';
    const MOVE_FILES = 'move_files';
    const MIGRATE_DELETED_FILES = 'migrate_deleted_files'; 
    
	function MigrationForm($action)
    {
        parent :: __construct('migration_settings', 'post', $action);

        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $values = $this->exportValues();
        
    	$this->addElement('category', Translation :: get('General'));
        
    	$this->addElement('select', self :: PLATFORM, Translation :: get('Platform'), $this->get_platforms());
        $this->addRule(self :: PLATFORM, Translation :: get('ThisFieldIsRequired'), 'required');
        
    	$this->addElement('text', self :: PLATFORM_PATH, Translation :: get('Path'), array("size" => "50"));
        $this->addRule(self :: PLATFORM_PATH, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('checkbox', self :: MOVE_FILES, null, Translation :: get('MoveFiles'));
		$this->addElement('checkbox', self :: MIGRATE_DELETED_FILES, null, Translation :: get('MigrateDeletedFiles'));        

		$this->addElement('category');
		
		$select_blocks = (Request :: post('select_blocks') || Request :: post('submit'));
		
    	if($select_blocks)
    	{ 
    		$this->freeze(self :: PLATFORM);
    	
			$this->addElement('category', Translation :: get('Blocks'));
			
			$blocks = $this->get_blocks(Request :: post(self :: PLATFORM));
			foreach($blocks as $block)
			{
				$block = substr($block, 0, -20);
				$this->addElement('checkbox', 'migrate[' . $block . ']', null, Translation :: get('Migrate' . Utilities :: underscores_to_camelcase($block)));
			}
			
			$this->addElement('category');
			
			$buttons[] = $this->createElement('style_submit_button', 'select_blocks', Translation :: get('SelectBlocks'), array('class' => 'positive update', 'style' => 'display: none;'));
			$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Migrate'), array('class' => 'positive update'));
    	}
		else
		{
			$buttons[] = $this->createElement('style_submit_button', 'select_blocks', Translation :: get('SelectBlocks'), array('class' => 'positive update'));
			$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Migrate'), array('class' => 'positive update', 'style' => 'display: none;'));
		}
       
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function validate()
    {
    	$values = $this->exportValues();
    	if(isset($values['submit']))
    	{
    		return parent :: validate();
    	}
    	
    	return false;
    }
    
    function create_migration_settings()
    {
    	$values = $this->exportValues();
    	$adm = AdminDataManager :: get_instance();
    	
    	$settings = array(self :: PLATFORM, self :: PLATFORM_PATH, self :: MOVE_FILES, self :: MIGRATE_DELETED_FILES);
    	foreach($settings as $setting)
    	{
    		$value = $values[$setting];
    		$setting = $adm->retrieve_setting_from_variable_name($setting, MigrationManager :: APPLICATION_NAME);
    		$setting->set_value($value);
    		$setting->update();
    	}
    	
    	$setting = $adm->retrieve_setting_from_variable_name('in_migration', MigrationManager :: APPLICATION_NAME);
    	$setting->set_value(1);
    	$setting->update();
    	
    	foreach($values['migrate'] as $block => $value)
    	{ 
    		$migration_block = new MigrationBlock();
    		$migration_block->set_name($block);
    		$migration_block->create();
    	}
    	
    }
    
    function get_platforms()
    {
    	$dir = dirname(__FILE__) . '/../platform/';
    	$contents = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);
    	
    	$folders = array();
    	
    	foreach($contents as $content)
    	{
    		$folders[$content] = $content;
    	}
    	
    	return $folders;
    }
    
    function get_blocks($platform)
    {
    	$dir = dirname(__FILE__) . '/../platform/' . $platform . '/migration/';
    	
    	if(!file_exists($dir))
    	{
    		return;
    	}
    	
    	return Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES, false);
    }
}

?>