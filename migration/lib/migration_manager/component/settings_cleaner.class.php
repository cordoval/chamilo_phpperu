<?php
/**
 * $Id: migration.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component
 */

/**
 * Migration MigrationManagerComponent which allows the administrator to clean settings
 *
 * @author Sven Vanpoucke
 */
class MigrationManagerSettingsCleanerComponent extends MigrationManager
{
	const CLEANING_METHOD = 'cleaning_method';
	const CLEAN_MIGRATION_BLOCKS = 1;
	const CLEAN_ALL = 2;
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MigrationManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Migration') ));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CleanMigrationsettings')));
		$trail->add_help('user general');
		
		$form = $this->create_settings_cleaner_form();
		
		if($form->validate())
		{
			$this->clean_settings($form);
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
    }
    
    function create_settings_cleaner_form()
    {
    	$form = new FormValidator('migration_settings_cleaner', 'post', $this->get_url());
    	
    	$form->addElement('select', self :: CLEANING_METHOD, Translation :: get('CleaningMethod'), $this->get_cleaning_methods());
    	$form->addElement('style_submit_button', 'submit', Translation :: get('Clean'), array('class' => 'positive update'));
    	
    	return $form;
    }
    
    function get_cleaning_methods()
    {
    	$cleaning_methods = array();
    	
    	$cleaning_methods[self :: CLEAN_MIGRATION_BLOCKS] = Translation :: get('CleanMigrationBlocks');
    	$cleaning_methods[self :: CLEAN_ALL] = Translation :: get('CleanAll');
    	
    	return $cleaning_methods;
    }
    
    function clean_settings($form)
    {
    	$value = $form->exportValue(self :: CLEANING_METHOD);
    }
}
?>