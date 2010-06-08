<?php
/**
 * This is a skeleton for a data manager for the Cba Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Nick Van Loocke
 */
class CbaDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function CbaDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return CbaDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '_cba_data_manager.class.php';
            $class = $type . 'CbaDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>