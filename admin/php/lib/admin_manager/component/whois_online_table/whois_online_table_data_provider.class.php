<?php
/**
 * $Id: whois_online_table_data_provider.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.admin_manager.component.whois_online_table
 */
/**
 * Data provider for a user browser table.
 *
 * This class implements some functions to allow user browser tables to
 * retrieve information about the users to display.
 */
class WhoisOnlineTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param UserManagerComponent $browser
     * @param Condition $condition
     */
    function WhoisOnlineTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the users
     * @param String $user
     * @param String $category
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return UserDataManager :: get_instance()->retrieve_users($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of users in the table
     * @return int
     */
    function get_object_count()
    {
        return UserDataManager :: get_instance()->count_users($this->get_condition());
    }
}
?>