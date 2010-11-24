<?php
namespace rights;

use common\libraries\Path;
use common\libraries\Application;
use common\libraries\SubManager;
use common\libraries\Request;

/**
 * $Id: user_right_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.user_right_manager
 * @author Hans De Bisschop
 */

class UserRightManager extends SubManager
{
    const PARAM_USER_RIGHT_ACTION = 'action';
    const PARAM_USER = 'user';
    const PARAM_SOURCE = 'source';
    const PARAM_LOCATION = 'location';

    const ACTION_BROWSE_USER_RIGHTS = 'browser';
    const ACTION_BROWSE_LOCATION_USER_RIGHTS = 'user';
    const ACTION_SET_USER_RIGHTS = 'setter';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_USER_RIGHTS;

    function __construct($rights_manager)
    {
        parent :: __construct($rights_manager);

        $rights_template_action = Request :: get(self :: PARAM_USER_RIGHT_ACTION);
        if ($rights_template_action)
        {
            $this->set_parameter(self :: PARAM_USER_RIGHT_ACTION, $rights_template_action);
        }
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/user_right_manager/component/';
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function count_locations($conditions = null)
    {
        return $this->get_parent()->count_locations($conditions);
    }

    function retrieve_location($location_id)
    {
        return $this->get_parent()->retrieve_location($location_id);
    }

    function retrieve_user_right_location($right_id, $user_id, $location_id)
    {
        return $this->get_parent()->retrieve_user_right_location($right_id, $user_id, $location_id);
    }

    function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return UserDataManager :: get_instance()->retrieve_users($condition, $offset, $count, $order_property);
    }

    function count_users($conditions = null)
    {
        return UserDataManager :: get_instance()->count_users($conditions);
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
        return self :: PARAM_USER_RIGHT_ACTION;
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