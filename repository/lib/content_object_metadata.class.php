<?php
/**
 * $Id: content_object_metadata.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
class ContentObjectMetadata extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';
    const PROPERTY_OVERRIDE_ID = 'override_id';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_PROPERTY = 'property';
    const PROPERTY_VALUE = 'value';

    function ContentObjectMetadata($defaultProperties = array ())
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
    
    function set_override_id($id)
    {
        if (isset($id) && is_numeric($id))
        {
            $this->set_default_property(self :: PROPERTY_OVERRIDE_ID, $id);
        }
    }

    function get_override_id()
    {
        return $this->get_default_property(self :: PROPERTY_OVERRIDE_ID);
    }

    /*************************************************************************/
    
    function set_type($type)
    {
        if (isset($type) && strlen($type) > 0)
        {
            $this->set_default_property(self :: PROPERTY_TYPE, $type);
        }
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /*************************************************************************/
    
    function set_property($property)
    {
        if (isset($property) && strlen($property) > 0)
        {
            $this->set_default_property(self :: PROPERTY_PROPERTY, $property);
        }
    }

    function get_property()
    {
        return $this->get_default_property(self :: PROPERTY_PROPERTY);
    }

    /*************************************************************************/
    
    function set_value($value)
    {
        if (isset($value))
        {
            $this->set_default_property(self :: PROPERTY_VALUE, $value);
        }
    }

    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /*************************************************************************/
    
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT;
        $extended_property_names[] = self :: PROPERTY_OVERRIDE_ID;
        $extended_property_names[] = self :: PROPERTY_PROPERTY;
        $extended_property_names[] = self :: PROPERTY_TYPE;
        $extended_property_names[] = self :: PROPERTY_VALUE;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function create()
    {
        $dm = RepositoryDataManager :: get_instance();
        
        $this->set_creation_date(time());
        
        return $dm->create_content_object_metadata($this);
    }

    function update()
    {
        if (! $this->is_identified())
        {
            throw new Exception('Learning object metadata could not be saved as its identity is not set');
        }
        
        $this->set_modification_date(time());
        
        //$dm = RepositoryDataManager :: get_instance();
        $result = $this->get_data_manager()->update_content_object_metadata($this);
        
        return $result;
    }

    function delete()
    {
        $dm = RepositoryDataManager :: get_instance();
        $result = $dm->delete_content_object_metadata($this);
        
        return $result;
    }

	/*************************************************************************
	* Fat model methods
	*************************************************************************/
    
    /**
     * Return a ContentObject having one of its identifier values equals to the given pair $catalog_name <--> $entry_name
     * 
     * @param string $catalog_name
     * @param string $entry_value
     * @return ContentObject
     */
    public static function get_by_catalog_entry_values($catalog_name, $entry_value)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $rs = $rdm->retrieve_content_object_by_catalog_entry_values($catalog_name, $entry_value);
        
        if(isset($rs))
        {
            $object = $rs->next_result();
            
            if(isset($object))
            {
                $content_object_id = $object->get_content_object_id();
            
                return $rdm->retrieve_content_object($content_object_id);
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }
    
}
?>