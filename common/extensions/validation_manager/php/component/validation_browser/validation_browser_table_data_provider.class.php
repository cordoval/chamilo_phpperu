<?php
/**
 * $Id: validation_browser_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager.component.validation_browser
 */
/**
 * Data provider for a validation browser table.
 *
 * This class implements some functions to allow validation browser tables to
 * retrieve information about the validation objects to display.
 */
class ValidationBrowserTableDataProvid extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ValidationManagerComponent $browser
     * @param Condition $condition
     */
    function ValidationBrowserTableDataProvid($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the validation objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching validation objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return $this->get_browser()->retrieve_validations($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of validation objects in the table
     * @return int
     */
    function get_object_count()
    {
        
        return $this->get_browser()->count_validations($this->get_condition());
    }
}
?>