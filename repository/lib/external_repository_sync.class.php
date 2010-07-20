<?php
class ExternalRepositorySync extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID = 'external_repository_object_id';
    const PROPERTY_SYNCHRONIZED = 'synchronized';
    const PROPERTY_EXTERNAL_REPOSITORY_OBJECT_TIMESTAMP = 'external_repository_object_timestamp';

    const SYNC_STATUS_IDENTICAL = 0;
    const SYNC_STATUS_EXTERNAL = 1;
    const SYNC_STATUS_INTERNAL = 2;


    /**
     * @var ContentObject
     */
    private $content_object;

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
     * @param string $external_repository_object_id
     */
    function set_external_repository_object_id($external_repository_object_id)
    {
        if (StringUtilities :: has_value($external_repository_object_id))
        {
            $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID, $external_repository_object_id);
        }
    }

    /**
     * @return string
     */
    function get_external_repository_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID);
    }

    /**
     * @param int $datetime
     */
    function set_synchronized($datetime)
    {
        if (isset($datetime) && is_numeric($datetime))
        {
            $this->set_default_property(self :: PROPERTY_SYNCHRONIZED, $datetime);
        }
    }

    /**
     * @return int
     */
    function get_synchronized()
    {
        return $this->get_default_property(self :: PROPERTY_SYNCHRONIZED);
    }

    /**
     * @param int $external_repository_id
     */
    function set_external_repository_id($external_repository_id)
    {
        if (isset($external_repository_id) && is_numeric($external_repository_id))
        {
            $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
        }
    }

    /**
     * @return int
     */
    function get_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    /**
     * @param array $property_names
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_REPOSITORY_ID;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID;
        $extended_property_names[] = self :: PROPERTY_SYNCHRONIZED;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * @return string
     */
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    /**
     * @return boolean
     */
    function create()
    {
        $this->set_creation_date(time());
        $this->set_modification_date(time());
        return parent :: create();
    }

    /**
     * @return boolean
     */
    function update()
    {
        if (! $this->is_identified())
        {
            throw new Exception('ExternalRepositorySync object could not be saved as its identity is not set');
        }

        $this->set_modification_date(time());
        return parent :: update();
    }

    /**
     * @return ContentObject
     */
    function get_content_object()
    {
        if(!isset($this->content_object))
        {
            $this->content_object = RepositoryDataManager::get_instance()->retrieve_content_object($this->get_content_object_id());
        }
        return $this->content_object;
    }

    function get_synchronization_status()
    {
        $content_object_modification_date = $this->get_content_object()->get_modification_date();

        if ($content_object_modification_date > $this->get_synchronized())
        {
            return self :: SYNC_STATUS_INTERNAL;
        }
        elseif($content_object_modification_date < $this->get_synchronized())
        {
            return self :: SYNC_STATUS_EXTERNAL;
        }
        else
        {
            return self :: SYNC_STATUS_IDENTICAL;
        }
    }

    /*************************************************************************
     * Fat model methods
     *************************************************************************/

    /**
     * @param int $content_object_id
     * @return ExternalRepositorySync
     */
    public static function get_by_content_object_id($content_object_id)
    {
        $conditions = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        return $this->get_data_manager()->retrieve_external_repository_sync($conditions);
    }

    /**
     * @param int $content_object_id
     * @param int $external_repository_id
     * @return ExternalRepositorySync
     */
    public static function get_by_content_object_id_and_external_repository_id($content_object_id, $external_repository_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        $conditions[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
        $condition = new AndCondition($conditions);
        return $this->get_data_manager()->retrieve_external_repository_sync($condition);
    }

    /**
     * @param int $external_repository_object_id
     * @param int $external_repository_id
     * @return ExternalRepositorySync
     */
    public static function get_by_external_repository_object_id_and_external_repository_id($external_repository_object_id, $external_repository_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID, $external_repository_object_id);
        $conditions[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
        $condition = new AndCondition($conditions);
        return $this->get_data_manager()->retrieve_external_repository_sync($conditions);
    }

    /**
     * @param ContentObject $content_object
     * @param ExternalRepositoryObject $external_repository_object
     * @param int $external_repository_id
     * @return boolean
     */
    public static function quicksave(ContentObject $content_object, ExternalRepositoryObject $external_repository_object, $external_repository_id)
    {
        $sync = new ExternalRepositorySync();
        $sync->set_content_object_id($content_object->get_id());
        $sync->set_external_repository_id($external_repository_id);
        $sync->set_external_repository_object_id($external_repository_object->get_id());
        $sync->set_synchronized(time());
        return $sync->create();
    }

}
?>