<?php
/**
 * $Id: webservice_data_manager.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */
abstract class WebserviceDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Array which contains the registered applications running on top of this
     * repositorydatamanager
     */
    private $applications;

    /**
     * Constructor.
     */
    protected function WebserviceDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return WebservicesDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'WebserviceDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function count_webservices($conditions = null);

    abstract function truncate_webservice($id);

    abstract function truncate_webservice_credential($webserviceCredential);

    abstract function retrieve_webservice($id);

    abstract function retrieve_webservice_by_name($name);

    abstract function retrieve_webservices($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_webservice_category($id);

    abstract function retrieve_webservice_categories($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_webservice_credential_by_hash($hash);

    abstract function retrieve_webservice_credentials_by_ip($ip);

    abstract function retrieve_webservice_credential_by_user_id($user_id);

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function delete_webservice($webservice);

    abstract function delete_webservice_category($webserviceCategory);

    abstract function delete_webservice_credential($webserviceCredential);

    abstract function delete_expired_webservice_credentials();

    abstract function update_webservice($webservice);

    abstract function update_webservice_category($webserviceCategory);

    abstract function create_webservice($webservice);

    abstract function create_webservice_category($webserviceCategory);

    abstract function create_webservice_credential($webserviceCredential);

}
?>