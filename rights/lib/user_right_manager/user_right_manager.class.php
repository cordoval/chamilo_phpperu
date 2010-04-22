<?php
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
    
    const ACTION_BROWSE_USER_RIGHTS = 'browse';
    const ACTION_BROWSE_LOCATION_USER_RIGHTS = 'user';
    const ACTION_SET_USER_RIGHTS = 'set';

    function UserRightManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $rights_template_action = Request :: get(self :: PARAM_USER_RIGHT_ACTION);
        if ($rights_template_action)
        {
            $this->set_parameter(self :: PARAM_USER_RIGHT_ACTION, $rights_template_action);
        }
    }

    function run()
    {
        $rights_template_action = $this->get_parameter(self :: PARAM_USER_RIGHT_ACTION);
        
        switch ($rights_template_action)
        {
            case self :: ACTION_BROWSE_USER_RIGHTS :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_SET_USER_RIGHTS :
                $component = $this->create_component('Setter');
                break;
            case self :: ACTION_BROWSE_LOCATION_USER_RIGHTS :
                $component = $this->create_component('User');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
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
}
?>