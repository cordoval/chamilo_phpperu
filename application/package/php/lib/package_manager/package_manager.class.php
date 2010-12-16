<?php
namespace application\package;

use common\libraries\WebApplication;

class PackageManager extends WebApplication
{
    const APPLICATION_NAME = 'package';

    const ACTION_PACKAGE_INSTANCE = 'package_instance';

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
}
?>