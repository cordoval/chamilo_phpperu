<?php
namespace application\package;

use common\libraries\WebApplication;
use common\libraries\DynamicAction;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\Redirect;

class PackageManager extends WebApplication
{
    const APPLICATION_NAME = 'package';
    
    const ACTION_PACKAGE_INSTANCE = 'package_instance';
    const ACTION_AUTHOR = 'author';
    const ACTION_DEPENDENCY = 'dependency';
    
    const DEFAULT_ACTION = self :: ACTION_PACKAGE_INSTANCE;

    /**
     * Constructor
     * @param int $user_id
     */
    public function __construct($user)
    {
        parent :: __construct($user);
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
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

    /**
     * Gets the available links to display in the platform admin
     * @retun array of links and actions
     */
    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('InstanceManager', null, Utilities :: COMMON_LIBRARIES), Translation :: get('InstanceManager'), Theme :: get_image_path() . 'admin/add.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_PACKAGE_INSTANCE)));
        $links[] = new DynamicAction(Translation :: get('AuthorManager', null, Utilities :: COMMON_LIBRARIES), Translation :: get('AuthorManager'), Theme :: get_image_path() . 'admin/export.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_AUTHOR)));
        $links[] = new DynamicAction(Translation :: get('DependencyManager', null, Utilities :: COMMON_LIBRARIES), Translation :: get('DependencyManager'), Theme :: get_image_path() . 'admin/export.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_DEPENDENCY)));
        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;
        
        return $info;
    }
}
?>