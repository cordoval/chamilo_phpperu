<?php
namespace group;
use common\libraries\Utilities;
use common\libraries\Configuration;
use common\libraries\EqualityCondition;
use common\libraries\DataManagerInterface;
use user\UserDataManager;
use common\libraries\AndCondition;
use common\libraries\ObjectTableOrder;
/**
 * $Id: group_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package group.lib
 */
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
class GroupDataManager implements DataManagerInterface
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

//    function __call($method, $args)
//    {
//        print "Method $method called:\n";
//        var_dump($args);
//        exit;
//    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return GroupDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_group_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'GroupDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
    
    static function get_root_group()
    {
    	return self :: get_instance()->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, 0))->next_result();
    }

    private static $group_cache;
    static function retrieve_group_by_code($code)
    {
        if(!self :: $group_cache[$code])
        {
            $condition = new EqualityCondition(Group :: PROPERTY_CODE, $code);
            self :: $group_cache[$code] = self :: get_instance()->retrieve_groups($condition)->next_result();
        }

        return self :: $group_cache[$code];
    }

    static function subscribe_user_to_group_by_official_code_and_group_code($official_code, $group_code)
    {
        $group_rel_user = self :: make_group_rel_user_from_official_code_and_group_code($official_code, $group_code);

        if($group_rel_user)
        {
             return $group_rel_user->create();
        }

        return false;
    }

    static function remove_user_from_group_by_official_code_and_group_code($official_code, $group_code)
    {
        $group_rel_user = self :: make_group_rel_user_from_official_code_and_group_code($official_code, $group_code);

        if($group_rel_user)
        {
            return self :: get_instance()->delete_group_rel_user($group_rel_user);
        }

        return false;
    }

    // cache to make subscribing users in batch more performant
    private static $group_rel_user_cache;

    private static function make_group_rel_user_from_official_code_and_group_code($official_code, $group_code)
    {
        if(!self :: $group_rel_user_cache[$group_code][$official_code])
        {
            $group = self :: retrieve_group_by_code($group_code);
            $user = UserDataManager :: retrieve_user_by_official_code($official_code);

            if(!$group || !$user)
            {
                return false;
            }

            $group_rel_user = new GroupRelUser();
            $group_rel_user->set_group_id($group->get_id());
            $group_rel_user->set_user_id($user->get_id());
            self :: $group_rel_user_cache[$group_code][$official_code] = $group_rel_user;
        }

        return self :: $group_rel_user_cache[$group_code][$official_code];
    }

    static function fix_nested_values_batch($parent_id = 0, $counter = 1)
    {
        $groups = self :: get_instance()->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, $parent_id), null, null, new ObjectTableOrder(Group :: PROPERTY_LEFT_VALUE));
        while($group = $groups->next_result())
        {
            $update = false;

            if($group->get_left_value() != $counter)
            {
                $group->set_left_value($counter);
                $update = true;
            }

            $counter++;
            
            self :: fix_nested_values_batch($group->get_id(), $counter);
            
            if($group->get_right_value() != $counter)
            {
                $group->set_right_value($counter);
                $update = true;
            }
            
            if($update)
            {
                $group->update();
            }

            $counter++;
            
        }

    }
}
?>