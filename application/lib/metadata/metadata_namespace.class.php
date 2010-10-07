<?php 
/**
 * metadata
 */

/**
 * This class describes a MetadataNamespace data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataNamespace extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * MetadataNamespace properties
     */
    const PROPERTY_NS_PREFIX = 'ns_prefix';
    const PROPERTY_NAME = 'name';
    const PROPERTY_URL = 'url';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
            return array (self :: PROPERTY_NS_PREFIX, self :: PROPERTY_NAME, self :: PROPERTY_URL);
    }

    function get_data_manager()
    {
            return MetadataDataManager :: get_instance();
    }

    /**
     * Returns the ns_prefix of this MetadataNamespace.
     * @return the ns_prefix.
     */
    function get_ns_prefix()
    {
            return $this->get_default_property(self :: PROPERTY_NS_PREFIX);
    }

    /**
     * Sets the ns_prefix of this MetadataNamespace.
     * @param ns_prefix
     */
    function set_ns_prefix($ns_prefix)
    {
            $this->set_default_property(self :: PROPERTY_NS_PREFIX, $ns_prefix);
    }

    /**
     * Returns the name of this MetadataNamespace.
     * @return the name.
     */
    function get_name()
    {
            return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this MetadataNamespace.
     * @param name
     */
    function set_name($name)
    {
            $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the url of this MetadataNamespace.
     * @return the url.
     */
    function get_url()
    {
            return $this->get_default_property(self :: PROPERTY_URL);
    }

    /**
     * Sets the url of this MetadataNamespace.
     * @param url
     */
    function set_url($url)
    {
            $this->set_default_property(self :: PROPERTY_URL, $url);
    }


    static function get_table_name()
    {
            return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    function delete()
    {
        //only deletes if 0 children
        $mdm = $this->get_data_manager();

        $condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_NS_PREFIX, $this->get_ns_prefix());
        $count =  $mdm->count_metadata_property_types($condition);

        $condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX, $this->get_ns_prefix());
        $count +=  $mdm->count_metadata_property_attribute_types($condition);
        
        if($count === 0)
        {
            return parent :: delete();
        }
        else
        {
            return false;
        }
    }
}

?>