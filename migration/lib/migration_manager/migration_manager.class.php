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

    /**
     * Constant defining an action of the repository manager.
     */
    const ACTION_MIGRATE = 'migration';
    const ACTION_CLEAN_SETTINGS = 'settings_cleaner';

    const DEFAULT_ACTION = self :: ACTION_MIGRATE;

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

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Migrate'), Translation :: get('Migrate'), Theme :: get_image_path() . 'browse_sort.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_MIGRATE), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('SettingsCleaner'), Translation :: get('SettingsCleaner'), Theme :: get_image_path() . 'browse_sort.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => self :: ACTION_CLEAN_SETTINGS), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;

        return $info;
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

}
?>