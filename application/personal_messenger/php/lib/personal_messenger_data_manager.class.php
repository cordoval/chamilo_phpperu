<?php
/**
 * $Id: personal_messenger_data_manager.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class PersonalMessengerDataManager
{

    private static $instance;

    protected function PersonalMessengerDataManager()
    {
        $this->initialize();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_personal_messenger_data_manager.class.php';
            $class = $type . 'PersonalMessengerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>