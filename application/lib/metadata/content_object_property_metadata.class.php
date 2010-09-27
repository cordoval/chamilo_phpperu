<?php 
/**
 * metadata
 */

/**
 * This class describes a ContentObjectPropertyMetadata data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContentObjectPropertyMetadata extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * ContentObjectPropertyMetadata properties
	 */
	const PROPERTY_PROPERTY_TYPE_ID = 'property_type_id';
	const PROPERTY_CONTENT_OBJECT_PROPERTY = 'content_object_property';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,self :: PROPERTY_PROPERTY_TYPE_ID, self :: PROPERTY_CONTENT_OBJECT_PROPERTY);
	}

	function get_data_manager()
	{
		return MetadataDataManager :: get_instance();
	}

	/**
	 * Returns the property_type_id of this ContentObjectPropertyMetadata.
	 * @return the property_type_id.
	 */
	function get_property_type_id()
	{
		return $this->get_default_property(self :: PROPERTY_PROPERTY_TYPE_ID);
	}

	/**
	 * Sets the property_type_id of this ContentObjectPropertyMetadata.
	 * @param property_type_id
	 */
	function set_property_type_id($property_type_id)
	{
		$this->set_default_property(self :: PROPERTY_PROPERTY_TYPE_ID, $property_type_id);
	}

	/**
	 * Returns the content_object_property of this ContentObjectPropertyMetadata.
	 * @return the content_object_property.
	 */
	function get_content_object_property()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_PROPERTY);
	}

	/**
	 * Sets the content_object_property of this ContentObjectPropertyMetadata.
	 * @param content_object_property
	 */
	function set_content_object_property($content_object_property)
	{
		$this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_PROPERTY, $content_object_property);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>