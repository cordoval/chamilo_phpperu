<?php
/**
 * $Id: reporting_data_manager.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 *
 * @author Michael Kyndt
 * @author Hans De Bisschop
 */

class ReportingDataManager
{
    private static $instance;

    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_reporting_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'ReportingDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>