<?php
/**
 * $Id: dynamic_form_element_browser_table_data_provider.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component.dynamic_form_element_browser
 * @author Sven Vanpoucke
 */

class DynamicFormElementBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param UserManagerComponent $browser
     * @param Condition $condition
     */
    function DynamicFormElementBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the users
     * @param String $user
     * @param String $category
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return AdminDataManager :: get_instance()->retrieve_dynamic_form_elements(
        	new EqualityCondition(DynamicFormElement :: PROPERTY_DYNAMIC_FORM_ID,$this->get_browser()->get_form()->get_id()), $offset, $count, $order_property);
    }

    /**
     * Gets the number of users in the table
     * @return int
     */
    function get_object_count()
    {
        return AdminDataManager :: get_instance()->count_dynamic_form_elements(
        	new EqualityCondition(DynamicFormElement :: PROPERTY_DYNAMIC_FORM_ID,$this->get_browser()->get_form()->get_id()));
    }
}
?>