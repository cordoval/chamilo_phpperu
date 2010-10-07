<?php 
/**
 * metadata
 */

/**
 * This class describes a MetadataPropertyValue data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyValue extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * MetadataPropertyValue properties
     */
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_PROPERTY_TYPE_ID = 'property_type_id';
    const PROPERTY_VALUE = 'value';


    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
            return array (self :: PROPERTY_ID, self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_PROPERTY_TYPE_ID, self :: PROPERTY_VALUE);
    }

    function get_data_manager()
    {
            return MetadataDataManager :: get_instance();
    }

    /**
     * Returns the content_object_id of this MetadataPropertyValue.
     * @return the content_object_id.
     */
    function get_content_object_id()
    {
            return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id of this MetadataPropertyValue.
     * @param content_object_id
     */
    function set_content_object_id($content_object_id)
    {
            $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
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


    static function get_table_name()
    {
            return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function delete()
    {
        if(parent :: delete())
        {
            //delete associated elements
            $mdm = $this->get_data_manager();

            $condition = new EqualityCondition(self :: PROPERTY_PARENT_ID, $this->get_id());
            $metadata_property_attribute_values = $mdm->retrieve_metadata_property_attribute_values($condition);

            while($metadata_property_attribute_value = $metadata_property_attribute_values->next_result())
            {
                $metadata_property_attribute_value->delete();
            }
            return true;
        }
        return false;
    }
}

?>