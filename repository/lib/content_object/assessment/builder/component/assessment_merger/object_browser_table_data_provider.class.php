<?php
/**
 * $Id: object_browser_table_data_provider.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.assessment_merger
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ObjectBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ObjectManagerComponent $browser
     * @param Condition $condition
     */
    function ObjectBrowserTableDataProvider($browser, $condition)
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

        // We always use title as second sorting parameter
        //		$order_property[] = ContentObject :: PROPERTY_TITLE;


        return RepositoryDataManager :: get_instance()->retrieve_content_objects($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return RepositoryDataManager :: get_instance()->count_content_objects($this->get_condition());
    }
}
?>