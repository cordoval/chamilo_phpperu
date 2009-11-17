<?php
/**
 * $Id: profile_publication_browser_table_data_provider.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component.profile_publication_browser
 */
/**
 * Data provider for a profile browser table.
 *
 * This class implements some functions to allow profile browser tables to
 * retrieve information about the profile objects to display.
 */
class ProfilePublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ProfileManagerComponent $browser
     * @param Condition $condition
     */
    function ProfilePublicationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the profile objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching profile objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return $this->get_browser()->retrieve_profile_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of profile objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_profile_publications($this->get_condition());
    }
}
?>