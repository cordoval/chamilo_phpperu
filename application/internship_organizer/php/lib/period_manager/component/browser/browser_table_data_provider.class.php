<?php

class InternshipOrganizerPeriodBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function InternshipOrganizerPeriodBrowserTableDataProvider($browser, $condition)
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
        $order_property = $this->get_order_property($order_property);
        return $this->get_browser()->retrieve_periods($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_periods($this->get_condition());
    }
}
?>