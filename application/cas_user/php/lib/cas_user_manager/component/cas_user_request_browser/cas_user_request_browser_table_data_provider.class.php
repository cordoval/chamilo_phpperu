<?php
namespace application\cas_user;

use common\libraries\ObjectTableDataProvider;

/**
 * Data provider for a cas_user_request table
 *
 * @author Hans De Bisschop
 */
class CasUserRequestBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function CasUserRequestBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Retrieves the objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of objects
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);

        return $this->get_browser()->retrieve_cas_user_requests($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_cas_user_requests($this->get_condition());
    }
}
?>