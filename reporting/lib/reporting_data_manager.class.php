<?php
/**
 * $Id: reporting_data_manager.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */

abstract class ReportingDataManager
{
    private static $instance;

    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'ReportingDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    protected function ReportingDataManager()
    {
        $this->initialize();
    }

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function create_reporting_block($reporting_block);

    abstract function update_reporting_block($reporting_block);

    abstract function retrieve_reporting_block_by_name($blockname);

    abstract function retrieve_reporting_blocks($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function count_reporting_blocks($condition = null);

    abstract function retrieve_reporting_block($block_id);

    abstract function create_reporting_template_registration($reporting_template_registration);

    abstract function update_reporting_template_registration($reporting_template_registration);

    abstract function retrieve_reporting_template_registrations($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function count_reporting_template_registrations($condition = null);

    abstract function retrieve_reporting_template_registration($reporting_template_registration_id);

    abstract function delete_reporting_template_registrations($condition = null);

    abstract function delete_reporting_blocks($condition = null);

    abstract function delete_orphaned_block_template_relations();
    
    abstract function retrieve_reporting_template_object($classname);
}
?>