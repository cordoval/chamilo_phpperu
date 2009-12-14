<?php
class ExternalRepositorySyncInfo extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_CONTENT_OBJECT               = 'content_object_id';
    const PROPERTY_EXTERNAL_REPOSITORY          = 'external_repository_id';
    const PROPERTY_EXTERNAL_OBJECT_UID          = 'external_object_uid';
    const PROPERTY_UTC_SYNCHRONIZED             = 'utc_synchronized';
    const PROPERTY_SYNCHRONIZED_OBJECT_DATETIME = 'synchronized_object_datetime';
    
    /*************************************************************************/
    
    function ExternalRepositorySyncInfo($defaultProperties = array ())
    {
        parent :: __construct($defaultProperties);
    }
    
    
    /*************************************************************************/
    
    function set_content_object_id($id)
    {
        if (isset($id) && is_numeric($id))
        {
            $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $id);
        }
    }

    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }
    
	/*************************************************************************/
    
    function set_external_object_uid($external_uid)
    {
        if (StringUtilities :: has_value($external_uid))
        {
            $this->set_default_property(self :: PROPERTY_EXTERNAL_OBJECT_UID, $external_uid);
        }
    }

    function get_external_object_uid()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_OBJECT_UID);
    }
    
    /*************************************************************************/
    
    function set_utc_synchronized($utc_datetime)
    {
        if (StringUtilities :: has_value($utc_datetime))
        {
            $this->set_default_property(self :: PROPERTY_UTC_SYNCHRONIZED, $utc_datetime);
        }
    }

    function get_utc_synchronized()
    {
        return $this->get_default_property(self :: PROPERTY_UTC_SYNCHRONIZED);
    }
    
    
    /*************************************************************************/
    
    function set_synchronized_object_datetime($synchronized_objet_datetime)
    {
        if(is_numeric($synchronized_objet_datetime))
        {
            $synchronized_objet_datetime = date('Y-m-d H:i:s', $synchronized_objet_datetime);
        }
        
        if(StringUtilities :: has_value($synchronized_objet_datetime))
        {
            $this->set_default_property(self :: PROPERTY_SYNCHRONIZED_OBJECT_DATETIME, $synchronized_objet_datetime);
        }
    }

    function get_synchronized_object_datetime()
    {
        return $this->get_default_property(self :: PROPERTY_SYNCHRONIZED_OBJECT_DATETIME);
    }
    
    
	/*************************************************************************/
    
    function set_external_repository_id($external_repository_id)
    {
        if (isset($external_repository_id) && is_numeric($external_repository_id))
        {
            $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY, $external_repository_id);
        }
    }

    function get_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY);
    }
    
    
    /*************************************************************************/
    
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_REPOSITORY;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_OBJECT_UID;
        $extended_property_names[] = self :: PROPERTY_UTC_SYNCHRONIZED;
        $extended_property_names[] = self :: PROPERTY_SYNCHRONIZED_OBJECT_DATETIME;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    function create()
    {
        $this->set_creation_date(time());
        
        return $this->get_data_manager()->create_external_export_sync_info($this);
    }
    
    function update()
    {
        if (!$this->is_identified())
        {
            throw new Exception('ExternalRepositorySyncInfo object could not be saved as its identity is not set');
        }
        
        $this->set_modification_date(time());
        
        return $this->get_data_manager()->update_external_export_sync_info($this);
    }
    
    function delete()
    {
        return $this->get_data_manager()->delete_external_export_sync_info($this);
    }
    
    /*************************************************************************
	* Fat model methods
	*************************************************************************/
    
    /**
     * 
     * @param int $content_object_id
     * @return ExternalRepositorySyncInfo
     */
    public static function get_by_content_object_id($content_object_id)
    {
        $dm = RepositoryDataManager :: get_instance();
        
        $conditions = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT, $content_object_id);
        
        return $dm->retrieve_external_export_sync_info($conditions);
    }
    
	/**
     * 
     * @param int $content_object_id
     * @return ExternalRepositorySyncInfo
     */
    public static function get_by_content_object_and_repository($content_object_id, $repository_id)
    {
        $dm = RepositoryDataManager :: get_instance();
        
        $condition_array = array();
        $condition_array[] = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT, $content_object_id);
        $condition_array[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_REPOSITORY, $repository_id);
        
        $conditions = new AndCondition($condition_array);
        
        return $dm->retrieve_external_export_sync_info($conditions);
    }
    

    /**
     * 
	 * @param integer $external_object_id
	 * @param integer $repository_id
	 * @return ExternalRepositorySyncInfo
     */
    public static function get_by_external_uid_and_repository($external_object_id, $repository_id)
    {
        $dm = RepositoryDataManager :: get_instance();
        
        $condition_array = array();
        $condition_array[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_OBJECT_UID, $external_object_id);
        $condition_array[] = new EqualityCondition(self :: PROPERTY_EXTERNAL_REPOSITORY, $repository_id);
        
        $conditions = new AndCondition($condition_array);
        
        return $dm->retrieve_external_export_sync_info($conditions);
    }
    
}
?>