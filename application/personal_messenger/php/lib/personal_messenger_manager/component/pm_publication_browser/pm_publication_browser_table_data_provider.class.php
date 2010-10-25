<?php

/**
 * $Id: pm_publication_browser_table_data_provider.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component.pm_publication_browser
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * Data provider for a personal messenger browser table.
 *
 * This class implements some functions to allow personal messenger browser tables to
 * retrieve information about the personal messages to display.
 */
class PmPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param PersonalMessengerManagerComponent $browser
     * @param Condition $condition
     */
    function PmPublicationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the personal message publications
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching personal message publications.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return $this->get_browser()->retrieve_personal_message_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of personal message publications in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_personal_message_publications($this->get_condition());
    }
}
?>