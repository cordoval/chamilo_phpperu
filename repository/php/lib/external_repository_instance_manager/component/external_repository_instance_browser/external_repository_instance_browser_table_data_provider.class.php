<?php
class ExternalRepositoryInstanceBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ExternalRepositoryInstanceManager $browser
     * @param Condition $condition
     */
    function ExternalRepositoryInstanceBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching external repositories.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return $this->get_browser()->retrieve_external_repositories($this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return $this->get_browser()->count_external_repositories($this->get_condition());
    }
}
?>