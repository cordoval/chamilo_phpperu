<?php
namespace application\wiki;

use common\libraries\ObjectTableDataProvider;

/**
 * $Id: wiki_publication_browser_table_data_provider.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component.wiki_publication_browser
 */
/**
 * Data provider for a wiki_publication table
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function __construct($browser, $condition)
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
        
        return $this->get_browser()->retrieve_wiki_publications($this->get_condition(), $offset, $count)->as_array();
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_wiki_publications($this->get_condition());
    }
}
?>