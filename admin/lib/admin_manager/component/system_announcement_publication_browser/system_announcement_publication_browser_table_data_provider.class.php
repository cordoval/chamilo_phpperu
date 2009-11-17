<?php
/**
 * $Id: system_announcement_publication_browser_table_data_provider.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.admin_manager.component.system_announcement_publication_table
 */
/**
 * Data provider for a profile browser table.
 *
 * This class implements some functions to allow profile browser tables to
 * retrieve information about the profile objects to display.
 */
class SystemAnnouncementPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ProfileManagerComponent $browser
     * @param Condition $condition
     */
    function SystemAnnouncementPublicationBrowserTableDataProvider($browser, $condition)
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
        
        return $this->get_browser()->retrieve_system_announcement_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of profile objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_system_announcement_publications($this->get_condition());
    }
}
?>