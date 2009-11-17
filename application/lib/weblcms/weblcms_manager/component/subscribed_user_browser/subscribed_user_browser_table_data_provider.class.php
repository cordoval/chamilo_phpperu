<?php
/**
 * $Id: subscribed_user_browser_table_data_provider.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_user_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class SubscribedUserBrowserTableDataProvider extends ObjectTableDataProvider
{
    private $udm;

    /**
     * Constructor
     * @param WeblcmsManagerComponent $browser
     * @param Condition $condition
     */
    function SubscribedUserBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
        $this->udm = UserDataManager :: get_instance($browser->get_user_id());
    }

    /**
     * Gets the users
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return $this->udm->retrieve_users($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of users in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->udm->count_users($this->get_condition());
    }
}
?>