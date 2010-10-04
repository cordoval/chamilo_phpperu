<?php
/**
 * @author Hans De Bisschop
 */

class CasUserDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function CasUserDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return CasUserDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_cas_user_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'CasUserDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    static function create_cas_account(CasUserRequest $cas_user_request)
    {
        return true;
    }
}
?>