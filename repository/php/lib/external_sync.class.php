<?php
namespace repository;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\StringUtilities;
use common\extensions\external_repository_manager\ExternalRepositoryManagerConnector;

/**
 * @author Hans De Bisschop
 *
 */
class ExternalSync extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_CONTENT_OBJECT_TIMESTAMP = 'content_object_timestamp';
    
    const PROPERTY_EXTERNAL_ID = 'external_id';
    const PROPERTY_EXTERNAL_OBJECT_ID = 'external_object_id';
    const PROPERTY_EXTERNAL_OBJECT_TIMESTAMP = 'external_object_timestamp';
    
    const SYNC_STATUS_ERROR = 0;
    const SYNC_STATUS_EXTERNAL = 1;
    const SYNC_STATUS_INTERNAL = 2;
    const SYNC_STATUS_IDENTICAL = 3;
    const SYNC_STATUS_CONFLICT = 4;
    
    /**
     * @var ContentObject
     */
    private $content_object;
    
    /**
     * @var ExternalRepositoryObject
     */
    private $external_object;
    
    /**
     * @var int
     */
    private $synchronization_status;
    
    /**
     * @var ExternalRepository
     */
    private $external;

    /**
     * @param int $content_object_id
     */
    function set_content_object_id($content_object_id)
    {
        if (isset($content_object_id) && is_numeric($content_object_id))
        {
            $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        }
    }

    /**
     * @return int
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @param string $external_object_id
     */
    function set_external_object_id($external_object_id)
    {
        if (StringUtilities :: has_value($external_object_id))
        {
            $this->set_default_property(self :: PROPERTY_EXTERNAL_OBJECT_ID, $external_object_id);
        }
    }

    /**
     * @return string
     */
    function get_external_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_OBJECT_ID);
    }

    /**
     * @param int $datetime
     */
    function set_content_object_timestamp($datetime)
    {
        if (isset($datetime) && is_numeric($datetime))
        {
            $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_TIMESTAMP, $datetime);
        }
    }

    /**
     * @return int
     */
    function get_content_object_timestamp()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_TIMESTAMP);
    }

    /**
     * @param int $datetime
     */
    function set_external_object_timestamp($datetime)
    {
        if (isset($datetime) && is_numeric($datetime))
        {
            $this->set_default_property(self :: PROPERTY_EXTERNAL_OBJECT_TIMESTAMP, $datetime);
        }
    }

    /**
     * @return int
     */
    function get_external_object_timestamp()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_OBJECT_TIMESTAMP);
    }

    /**
     * @param int $external_id
     */
    function set_external_id($external_id)
    {
        if (isset($external_id) && is_numeric($external_id))
        {
            $this->set_default_property(self :: PROPERTY_EXTERNAL_ID, $external_id);
        }
    }

    /**
     * @return int
     */
    function get_external_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_ID);
    }

    /**
     * @param array $property_names
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_TIMESTAMP;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_ID;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_OBJECT_ID;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_OBJECT_TIMESTAMP;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * @return string
     */
    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    /**
     * @return boolean
     */
    function create()
    {
        $now = time();
        $this->set_creation_date($now);
        $this->set_modification_date($now);
        return parent :: create();
    }

    /**
     * @return boolean
     */
    function update()
    {
        if (! $this->is_identified())
        {
            throw new Exception('ExternalSync object could not be saved as its identity is not set');
        }
        
        $this->set_modification_date(time());
        return parent :: update();
    }

    /**
     * @return ContentObject
     */
    function get_content_object()
    {
        if (! isset($this->content_object))
        {
            $this->content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_content_object_id());
        }
        return $this->content_object;
    }

    /**
     * @return ExternalObject
     */
    function get_external_object()
    {
        if (! isset($this->external_object))
        {
            $external_instance = $this->get_external();
            $type = $external_instance->get_instance_type();
            $class = ExternalInstanceManager :: get_manager_connector_class($type);
            $this->external_object = $class :: get_connector()->retrieve_external_object($this->get_external_object_id());         
        }
        return $this->external_object;
    }

    /**
     * @return ExternalRepository
     */
    function get_external()
    {
        if (! isset($this->external))
        {
            $this->external = RepositoryDataManager :: get_instance()->retrieve_external_instance($this->get_external_id());
        }
        return $this->external;
    }

    function get_synchronization_status($content_object_date = null, $external_object_date = null)
    {
        if (! isset($this->synchronization_status))
        {
            if (is_null($content_object_date))
            {
                $content_object_date = $this->get_content_object()->get_modification_date();
            }
            
            if (is_null($external_object_date))
            {
                $external_object_date = $this->get_external_object()->get_created();
            }
            
            if ($content_object_date > $this->get_content_object_timestamp())
            {
                if ($external_object_date > $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_CONFLICT;
                }
                elseif ($external_object_date == $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_EXTERNAL;
                }
                else
                {
                    $this->synchronization_status = self :: SYNC_STATUS_ERROR;
                }
            }
            elseif ($content_object_date == $this->get_content_object_timestamp())
            {
                if ($external_object_date > $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_INTERNAL;
                }
                elseif ($external_object_date == $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_IDENTICAL;
                }
                else
                {
                    $this->synchronization_status = self :: SYNC_STATUS_ERROR;
                }
            }
            else
            {
                $this->synchronization_status = self :: SYNC_STATUS_ERROR;
            }
        }
        
        return $this->synchronization_status;
    }

    /*************************************************************************
     * Fat model methods
     *************************************************************************/
    
    /**
     * @param int $content_object_id
     * @return ExternalSync
     */
    public static function get_by_content_object_id($content_object_id)
    {
        $conditions = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        return $this->get_data_manager()->retrieve_external_sync($conditions);
    }

    /**
     * @param int $content_object_id
     * @param int $external_id
     * @return ExternalSync
     */
    public static function get_by_content_object_id_and_external_id($content_object_id, $external_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        $conditions[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_ID, $external_id);
        $condition = new AndCondition($conditions);
        return $this->get_data_manager()->retrieve_external_sync($condition);
    }

    /**
     * @param int $external_object_id
     * @param int $external_id
     * @return ExternalSync
     */
    public static function get_by_external_object_id_and_external_id($external_object_id, $external_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_OBJECT_ID, $external_object_id);
        $conditions[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_ID, $external_id);
        $condition = new AndCondition($conditions);
        return $this->get_data_manager()->retrieve_external_sync($conditions);
    }

    /**
     * @param ContentObject $content_object
     * @param ExternalObject $external_object
     * @param int $external_id
     * @return boolean
     */
    public static function quicksave(ContentObject $content_object, $external_object, $external_id)
    {
        $sync = new ExternalSync();
        $sync->set_content_object_id($content_object->get_id());
        $sync->set_content_object_timestamp($content_object->get_modification_date());
        $sync->set_external_id((int) $external_id);
        $sync->set_external_object_id((string) $external_object->get_id());
        $sync->set_external_object_timestamp($external_object->get_modified());
        return $sync->create();
    }

}
?>