<?php 
namespace application\metadata;
use common\libraries\DataClass;
use common\libraries\Utilities;

class MetadataDefaultValue extends DataClass
{
    const PROPERTY_PROPERTY_TYPE_ID = 'property_type_id';
    const PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID = 'property_attribute_type_id';
    const PROPERTY_VALUE = 'value';

    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
            return array (self :: PROPERTY_ID, self :: PROPERTY_PROPERTY_TYPE_ID, self :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID, self :: PROPERTY_VALUE);
    }

    function get_data_manager()
    {
            return MetadataDataManager :: get_instance();
    }

    /**
     * Returns the property_type_id of this MetadataPropertyValue.
     * @return the property_type_id.
     */
    function get_property_type_id()
    {
            return $this->get_default_property(self :: PROPERTY_PROPERTY_TYPE_ID);
    }

    /**
     * Sets the property_type_id of this MetadataPropertyValue.
     * @param property_type_id
     */
    function set_property_type_id($property_type_id)
    {
            $this->set_default_property(self :: PROPERTY_PROPERTY_TYPE_ID, $property_type_id);
    }

    /**
     * Returns the value of this MetadataPropertyValue.
     * @return the value.
     */
    function get_value()
    {
            return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of this MetadataPropertyValue.
     * @param value
     */
    function set_value($value)
    {
            $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Returns the property_attribute_type_id of this MetadataPropertyAttributeValue.
     * @return the property_attribute_type_id.
     */
    function get_property_attribute_type_id()
    {
            return $this->get_default_property(self :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID);
    }

    /**
     * Sets the property_attribute_type_id of this MetadataPropertyAttributeValue.
     * @param property_attribute_type_id
     */
    function set_property_attribute_type_id($property_attribute_type_id)
    {
            $this->set_default_property(self :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID, $property_attribute_type_id);
    }

    static function get_table_name()
    {
            return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }
}

?>
