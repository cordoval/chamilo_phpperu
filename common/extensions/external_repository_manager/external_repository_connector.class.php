<?php
/**
 * @author Hans De Bisschop
 */
abstract class ExternalRepositoryConnector
{
    /**
     * @var array
     */
    private static $instances = array();
    
    /**
     * @var ExternalRepository
     */
    private $external_repository_instance;
    
    /**
     * @param ExternalRepository $external_repository_instance
     */
    function ExternalRepositoryConnector($external_repository_instance)
    {
        $this->external_repository_instance = $external_repository_instance;
    }
    
    /**
     * @return ExternalRepository
     */
    function get_external_repository_instance()
    {
        return $this->external_repository_instance;
    }
    
    /**
     * @param ExternalRepository $external_repository_instance
     */
    function set_external_repository_instance($external_repository_instance)
    {
        $this->external_repository_instance = $external_repository_instance;
    }
    
    /**
     * @return int
     */
    function get_external_repository_instance_id()
    {
        return $this->get_external_repository_instance()->get_id();
    }
    
    /**
     * @param ExternalRepository $external_repository
     * @return ExternalRepositoryConnector
     */
    static function factory($external_repository_instance)
    {
        $type = $external_repository_instance->get_type();
        
        $file = dirname(__FILE__) . '/type/' . $type . '/' . $type . '_external_repository_connector.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ExternalRepositoryConnectorTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryConnector';
        return new $class($external_repository_instance);
    }
    
    /**
     * @param ExternalRepository $external_repository_instance
     * @return ExternalRepositoryConnector
     */
    static function get_instance($external_repository_instance)
    {
        if (! isset(self :: $instances[$external_repository_instance->get_id()]))
        {
            self :: $instances[$external_repository_instance->get_id()] = self :: factory($external_repository_instance);
        }
        return self :: $instances[$external_repository_instance->get_id()];
    }

    /**
     * @param string $id
     */
    abstract function retrieve_external_repository_object($id);

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     */
    abstract function retrieve_external_repository_objects($condition, $order_property, $offset, $count);

    /**
     * @param mixed $condition
     */
    abstract function count_external_repository_objects($condition);

    /**
     * @param string $id
     */
    abstract function delete_external_repository_object($id);

    /**
     * @param string $id
     */
    abstract function export_external_repository_object($id);

    /**
     * @param string $query
     */
    abstract static function translate_search_query($query);
}
?>