<?php
/**
 * $Id: object_table_data_provider.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */
/**
 * todo: add comment
 */
abstract class ObjectTableDataProvider
{
    /**
     * The application manager component in which the table will be displayed
     */
    private $browser;
    /**
     * The condition used to select the learning objects
     */
    private $condition;

    /**
     * Constructor
     * @param ApplicationManagerComponent $browser
     * @param Condition $condition
     */
    function ObjectTableDataProvider($browser, $condition)
    {
        $this->browser = $browser;
        $this->condition = $condition;
    }

    /**
     * Gets the condition
     * @return Condition
     */
    function get_condition()
    {
        return $this->condition;
    }

    /**
     * Sets the condition
     * @param Condition $condition
     */
    function set_condition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Gets the browser
     * @return ApplicationManagerComponent
     */
    function get_browser()
    {
        return $this->browser;
    }

    /**
     * Sets the ApplicationManagerComponent
     * @param ApplicationManagerComponent $browser
     */
    function set_browser($browser)
    {
        $this->browser = $browser;
    }

    function get_order_property($order_property)
    {
        if (is_null($order_property))
        {
            $order_property = array();
        }
        elseif (! is_array($order_property))
        {
            $order_property = array($order_property);
        }
        
        return $order_property;
    }

    abstract function get_objects($offset, $count, $order_property = null);

    abstract function get_object_count();
}
?>