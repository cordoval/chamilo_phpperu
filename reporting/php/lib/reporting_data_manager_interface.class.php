<?php
/**
 * @package reporting.lib
 *
 * This is an interface for a data manager for the Reporting application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface ReportingDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_reporting_block($reporting_block);

    function update_reporting_block($reporting_block);

    function retrieve_reporting_block_by_name($blockname);

    function retrieve_reporting_blocks($condition = null, $offset = null, $count = null, $order_property = null);

    function count_reporting_blocks($condition = null);

    function retrieve_reporting_block($block_id);

    function create_reporting_template_registration($reporting_template_registration);

    function update_reporting_template_registration($reporting_template_registration);

    function retrieve_reporting_template_registrations($condition = null, $offset = null, $count = null, $order_property = null);

    function count_reporting_template_registrations($condition = null);

    function retrieve_reporting_template_registration($reporting_template_registration_id);

    function delete_reporting_template_registrations($condition = null);

    function delete_reporting_blocks($condition = null);

    function delete_orphaned_block_template_relations();

    function retrieve_reporting_template_object($classname);

}
?>