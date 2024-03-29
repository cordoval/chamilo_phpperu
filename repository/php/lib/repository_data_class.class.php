<?php
namespace repository;
use common\libraries\DataClass;
/**
 * $Id: repository_data_class.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
abstract class RepositoryDataClass extends DataClass
{
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';

    /*************************************************************************/
    
    public function __construct($defaultProperties = array ())
    {
        parent :: __construct($defaultProperties);
    
    }

    /**
     * @param $name The name of the object property to get
     * @param $default_value A default value is the $name property is not set
     * @see chamilo/common/DataClass#get_default_property($name)
     */
    function get_default_property($name, $default_value = null)
    {
        $value = parent :: get_default_property($name);
        
        if (! isset($value) && isset($default_value))
        {
            $value = $default_value;
        }
        
        return $value;
    }

    /*************************************************************************/
    
    public function set_creation_date($created)
    {
        if (isset($created))
        {
            $this->set_default_property(self :: PROPERTY_CREATED, $created);
        }
    }

    public function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /*************************************************************************/
    
    public function set_modification_date($modified)
    {
        if (isset($modified))
        {
            $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
        }
    }

    public function get_modification_date()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    /*************************************************************************/
    
    public function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CREATED;
        $extended_property_names[] = self :: PROPERTY_MODIFIED;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /*************************************************************************/
    
    abstract static function get_table_name();

}
?>