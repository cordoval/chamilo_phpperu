<?php
/**
 * $Id: migration.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component
 */

//require_once dirname(__FILE__) . '/inc/migration_wizard.class.php';

/**
 * Migration MigrationManagerComponent which allows the administrator to migrate to LCMS
 *
 * @author Sven Vanpoucke
 */
class MigrationManagerMigrationComponent extends MigrationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        ini_set("memory_limit", "3500M"); 
		ini_set("max_execution_time", "72000"); 
		
        /*$wizard = new MigrationWizard($this);
        $wizard->run();*/
		
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MigrationManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Migration') ));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Migrate')));
		$trail->add_help('user general');
		
		$form = new MigrationForm($this->get_url());
		
		if($form->validate())
		{
			$form->create_migration_settings();
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
    }
}
?>