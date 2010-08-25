<?php
/**
 * $Id: migration_manager.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.migration_manager
 */

/**
 * A migration manager provides some functionalities to the administrator to migrate
 * from an old system to the LCMS
 *
 * @author Sven Vanpoucke
 */
class MigrationManager extends CoreApplication
{
    const APPLICATION_NAME = 'migration';

    /**#@+
     * Constant defining an action of the repository manager.
     */
    const ACTION_MIGRATE = 'migrate';
    const ACTION_CLEAN_SETTINGS = 'settings_cleaner';

    /**#@-*/
    /**
     * Constructor
     * @param int $user_id The user id of current user
     */
    function MigrationManager($user)
    {
        parent :: __construct($user);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Runs the migrationmanager, choose the correct component with the given parameters
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_MIGRATE :
                $component = $this->create_component('Migration');
                break;
            case self :: ACTION_CLEAN_SETTINGS :
                $component = $this->create_component('SettingsCleaner');
                break;
            default :
                $this->set_action(self :: ACTION_MIGRATE);
                $component = $this->create_component('Migration');
        }
        $component->run();
    }

 	public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Migrate'), Translation :: get('Migrate'), Theme :: get_image_path() . 'browse_sort.png', Redirect :: get_link(array(Application :: PARAM_ACTION => self :: ACTION_MIGRATE), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('SettingsCleaner'), Translation :: get('SettingsCleaner'), Theme :: get_image_path() . 'browse_sort.png', Redirect :: get_link(array(Application :: PARAM_ACTION => self :: ACTION_CLEAN_SETTINGS), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;

        return $info;
    }

}
?>