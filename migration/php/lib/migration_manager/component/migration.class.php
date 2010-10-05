<?php
/**
 * $Id: migration.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component
 */

require_once dirname(__FILE__) . '/migration_wizard/migration_wizard.class.php';

/**
 * Migration MigrationManagerComponent which allows the administrator to migrate to LCMS
 *
 * @author Sven Vanpoucke
 */
class MigrationManagerMigrationComponent extends MigrationManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        ini_set("memory_limit", "3500M"); 
		ini_set("max_execution_time", "72000"); 
		
		$setting = PlatformSetting :: get('in_migration', MigrationManager :: APPLICATION_NAME);
		if($setting == 1)
		{
			$wizard = new MigrationWizard($this);
        	$wizard->run();
		}
		else
		{
			$form = new MigrationForm($this->get_url());
			
			if($form->validate())
			{
				$succes = $form->create_migration_settings();
				$this->redirect();
			}
			else
			{
				$this->display_header();
				$form->display();
				$this->display_footer();
			}
		}	
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('migration_migrate');
    }
}
?>