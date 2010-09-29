<?php 
/**
 * context_linker
 */

/**
 * This class describes a ContextLink data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLink extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * ContextLink properties
	 */
	const PROPERTY_ORIGINAL_CONTENT_OBJECT_ID = 'original_content_object_id';
	const PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID = 'alternative_content_object_id';
	const PROPERTY_METADATA_PROPERTY_VALUE_ID = 'metadata_property_value_id';
	const PROPERTY_DATE = 'date';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, self :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, self :: PROPERTY_METADATA_PROPERTY_VALUE_ID, self :: PROPERTY_DATE);
	}

	function get_data_manager()
	{
		return ContextLinkerDataManager :: get_instance();
	}

	/**
	 * Returns the original_content_object_id of this ContextLink.
	 * @return the original_content_object_id.
	 */
	function get_original_content_object_id()
	{
		return $this->get_default_property(self :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID);
	}

	/**
	 * Sets the original_content_object_id of this ContextLink.
	 * @param original_content_object_id
	 */
	function set_original_content_object_id($original_content_object_id)
	{
		$this->set_default_property(self :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $original_content_object_id);
	}

	/**
	 * Returns the alternative_content_object_id of this ContextLink.
	 * @return the alternative_content_object_id.
	 */
	function get_alternative_content_object_id()
	{
		return $this->get_default_property(self :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID);
	}

	/**
	 * Sets the alternative_content_object_id of this ContextLink.
	 * @param alternative_content_object_id
	 */
	function set_alternative_content_object_id($alternative_content_object_id)
	{
		$this->set_default_property(self :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $alternative_content_object_id);
	}

	/**
	 * Returns the metadata_property_value_id of this ContextLink.
	 * @return the metadata_property_value_id.
	 */
	function get_metadata_property_value_id()
	{
		return $this->get_default_property(self :: PROPERTY_METADATA_PROPERTY_VALUE_ID);
	}

	/**
	 * Sets the metadata_property_value_id of this ContextLink.
	 * @param metadata_property_value_id
	 */
	function set_metadata_property_value_id($metadata_property_value_id)
	{
		$this->set_default_property(self :: PROPERTY_METADATA_PROPERTY_VALUE_ID, $metadata_property_value_id);
	}

	/**
	 * Returns the date of this ContextLink.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this ContextLink.
	 * @param date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>