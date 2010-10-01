<?php

/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ContentObjectUserShareRightsBrowserTableDataProvider extends ObjectTableDataProvider
{
    /**
     * returns the users that u share with
     * @param <type> $offset
     * @param <type> $count
     * @param <type> $order_property
     * @return <type> ObjectResultSet
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return UserDataManager :: get_instance()->retrieve_users($this->get_condition());
    }

    function get_object_count()
    {
        return 1;
    }

}

?>