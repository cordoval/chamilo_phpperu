<?php
/**
 * $Id: group_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.forms
 */

class MigrationForm extends FormValidator
{
    const SETTING_PLATFORM = 'platform';
    const SETTING_PLATFORM_PATH = 'platform_path';
    const SETTING_MOVE_FILES = 'move_files';
    const SETTING_MIGRATE_DELETED_FILES = 'migrate_deleted_files';
    const SETTING_IN_MIGRATION = 'in_migration'; 
    
    const PARAM_MIGRATE = 'migrate';
    const PARAM_SELECT_BLOCKS = 'select_blocks';
    
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
        
    	$this->addElement('select', self :: SETTING_PLATFORM, Translation :: get('Platform'), $this->get_platforms());
        $this->addRule(self :: SETTING_PLATFORM, Translation :: get('ThisFieldIsRequired'), 'required');
        
    	$this->addElement('text', self :: SETTING_PLATFORM_PATH, Translation :: get('Path'), array("size" => "50"));
        $this->addRule(self :: SETTING_PLATFORM_PATH, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('checkbox', self :: SETTING_MOVE_FILES, null, Translation :: get('MoveFiles'));
		$this->addElement('checkbox', self :: SETTING_MIGRATE_DELETED_FILES, null, Translation :: get('MigrateDeletedFiles'));        

		$this->addElement('category');
		
		$select_blocks = (Request :: post(self :: PARAM_SELECT_BLOCKS) || Request :: post(self :: PARAM_SUBMIT));
		
    	if($select_blocks)
    	{ 
    		$this->freeze(self :: SETTING_PLATFORM);
    	
			$this->addElement('category', Translation :: get('Blocks'));
			
			$blocks = $this->get_blocks(Request :: post(self :: SETTING_PLATFORM));
			foreach($blocks as $block)
			{
				$this->addElement('checkbox', self :: PARAM_MIGRATE . '[' . $block . ']', null, Translation :: get('Migrate' . Utilities :: underscores_to_camelcase($block)));
			}
			
			$this->addElement('category');
			
			$buttons[] = $this->createElement('style_submit_button', self :: PARAM_SELECT_BLOCKS, Translation :: get('SelectBlocks'), array('class' => 'positive update', 'style' => 'display: none;'));
			$buttons[] = $this->createElement('style_submit_button', self :: PARAM_SUBMIT, Translation :: get('Migrate'), array('class' => 'positive update'));
    	}
		else
		{
			$buttons[] = $this->createElement('style_submit_button', self :: PARAM_SELECT_BLOCKS, Translation :: get('SelectBlocks'), array('class' => 'positive update'));
			$buttons[] = $this->createElement('style_submit_button', self :: PARAM_SUBMIT, Translation :: get('Migrate'), array('class' => 'positive update', 'style' => 'display: none;'));
		}
       
        $buttons[] = $this->createElement('style_reset_button', self :: PARAM_RESET, Translation :: get('Reset'), array('class' => 'normal empty'));

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
    	
    	$settings = array(self :: SETTING_PLATFORM, self :: SETTING_PLATFORM_PATH, self :: SETTING_MOVE_FILES, self :: SETTING_MIGRATE_DELETED_FILES);
    	foreach($settings as $setting)
    	{
    		$value = $values[$setting];
    		
    		if(is_null($value))
    		{
    			$value = 0;
    		}
    		
    		$setting = $adm->retrieve_setting_from_variable_name($setting, MigrationManager :: APPLICATION_NAME);
    		$setting->set_value($value);
    		$setting->update();
    	}
    	
    	$setting = $adm->retrieve_setting_from_variable_name(self :: SETTING_IN_MIGRATION, MigrationManager :: APPLICATION_NAME);
    	$setting->set_value(1);
    	$setting->update();
    	
    	foreach($values[self :: PARAM_MIGRATE] as $block => $value)
    	{ 
    		$migration_block_registration = new MigrationBlockRegistration();
    		$migration_block_registration->set_name($block);
    		$migration_block_registration->create();
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
    	$properties = MigrationProperties :: factory($platform);
    	return $properties->get_migration_blocks();
    }
}

?>