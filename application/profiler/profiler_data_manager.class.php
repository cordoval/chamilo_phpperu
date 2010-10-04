<?php
/**
 * $Id: profiler_data_manager.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler
 */
class ProfilerDataManager
{

    private static $instance;

    protected function ProfilerDataManager()
    {
        $this->initialize();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_profiler_data_manager.class.php';
            $class = $type . 'ProfilerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>