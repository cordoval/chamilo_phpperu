<?php
namespace webservice;

use common\libraries\Configuration;
use common\libraries\EqualityCondition;

/**
 * $Id: webservice_data_manager.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */
class WebserviceDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return WebservicesDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_webservice_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' .  $type . 'WebserviceDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    static function retrieve_webservice_registration_by_code($code)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_CODE, $code);
        return self :: get_instance()->retrieve_webservices($condition)->next_result();
    }
}
?>