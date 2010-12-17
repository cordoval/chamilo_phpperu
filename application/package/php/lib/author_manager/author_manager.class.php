<?php
namespace application\package;

use common\libraries\SubManager;

class AuthorManager extends SubManager
{
    const PARAM_PACKAGE_INSTANCE_ACTION = 'action';

//    const ACTION_CREATE = 'creator';
    const ACTION_BROWSE = 'browser';
//    const ACTION_UPDATE = 'updater';
//    const ACTION_DELETE = 'deleter';
//    const ACTION_ACTIVATE = 'activater';
//    const ACTION_DEACTIVATE = 'deactivater';

    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    function __construct($rights_manager)
    {
        parent :: __construct($rights_manager);
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_PACKAGE_INSTANCE_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}
?>