<?php
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
            $class = Utilities :: underscores_to_camelcase($type) . 'GroupDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
    
    static function get_root_group()
    {
    	return self :: get_instance()->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, 0))->next_result();
    }
}
?>