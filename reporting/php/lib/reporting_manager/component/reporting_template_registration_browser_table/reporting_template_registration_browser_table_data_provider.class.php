<?php
/**
 * $Id: reporting_template_registration_browser_table_data_provider.class.php 215 2009-11-13 14:07:59Z vanpouckesven $ 
 * @package reporting.lib.reporting_manager.component.reporting_template_registration_browser_table
 * @author Michael Kyndt
 */
/**
 * Data provider for a reporting template registration browser table.
 *
 * This class implements some functions to allow reporting template registration
 * browser tables to retrieve information about the reporting template
 * registration objects to display.
 */
class ReportingTemplateRegistrationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ReportingTemplateRegistrationManagerComponent $browser
     * @param Condition $condition
     */
    function ReportingTemplateRegistrationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the reporting template registration objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return $this->get_browser()->retrieve_reporting_template_registrations($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of reporting template registration objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_reporting_template_registrations($this->get_condition());
    }
}
?>