<?php
/**
 * $Id: link_browser_table_data_provider.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class LinkBrowserTableDataProvider extends ObjectTableDataProvider
{	
    /**
     * The type of link
     * @var Integer
     */
	private $type;
    
    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function LinkBrowserTableDataProvider($browser, $condition, $type)
    {
        $this->type = $type;
    	parent :: __construct($browser, $condition);
    }

    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching content objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {

    }

    /**
     * Gets the number of content objects in the table
     * @return int
     */
    function get_object_count()
    {
        
    }
}
?>