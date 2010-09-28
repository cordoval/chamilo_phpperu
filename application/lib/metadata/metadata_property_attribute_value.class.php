<?php 
/**
 * metadata
 */

/**
 * This class describes a MetadataPropertyAttributeValue data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyAttributeValue extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * MetadataPropertyAttributeValue properties
	 */
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID = 'property_attribute_type_id';
        const PROPERTY_RELATION = 'relation';

        const RELATION_CONTENT_OBJECT_PROPERTY = '1';
        const RELATION_PROPERTY_VALUE = '2';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_PARENT_ID, self :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID, self :: PROPERTY_RELATION);
	}

	function get_data_manager()
	{
		return MetadataDataManager :: get_instance();
	}

	/**
	 * Returns the property_value_id of this MetadataPropertyAttributeValue.
	 * @return the property_value_id.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}

	/**
	 * Sets the property_value_id of this MetadataPropertyAttributeValue.
	 * @param property_value_id
	 */
	function set_parent_id($parent_id)
	{
		$this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
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

        function get_relation()
	{
		return $this->get_default_property(self :: PROPERTY_RELATION);
	}

	function set_relation($relation)
	{
		$this->set_default_property(self :: PROPERTY_RELATION, $relation);
	}

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>