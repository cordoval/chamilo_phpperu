<?php
namespace application\portfolio;
use common\libraries\ObjectTableDataProvider;
use user\UserDataManager;



/**
 * Data provider for a user browser table.
 *
 * This class implements some functions to allow user browser tables to
 * retrieve information about the users to display.
 */
class PortfolioBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param PortfolioManagerComponent $browser
     * @param Condition $condition
     */
    function PortfolioBrowserTableDataProvider($browser, $condition)
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