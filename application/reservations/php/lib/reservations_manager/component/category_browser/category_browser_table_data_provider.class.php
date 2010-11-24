<?php

namespace application\reservations;

use common\libraries\ObjectTableOrder;
use common\libraries\ObjectTableDataProvider;
/**
 * $Id: category_browser_table_data_provider.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.category_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class CategoryBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function __construct($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        //$order_property = $this->get_order_property($order_property);
        $order_property = array(new ObjectTableOrder(Category :: PROPERTY_DISPLAY_ORDER));
        
        return $this->get_browser()->retrieve_categories($this->get_condition(), $offset, $count, $order_property);
    
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_categories($this->get_condition());
    }
}
?>