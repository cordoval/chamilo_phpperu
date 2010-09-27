<?php 
/**
 * metadata
 */

/**
 * This class describes a MetadataPropertyType data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyType extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * MetadataPropertyType properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NS_PREFIX = 'ns_prefix';
	const PROPERTY_NAME = 'name';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NS_PREFIX, self :: PROPERTY_NAME);
	}

	function get_data_manager()
	{
		return MetadataDataManager :: get_instance();
	}

	/**
	 * Returns the id of this MetadataPropertyType.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this MetadataPropertyType.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the ns_prefix of this MetadataPropertyType.
	 * @return the ns_prefix.
	 */
	function get_ns_prefix()
	{
		return $this->get_default_property(self :: PROPERTY_NS_PREFIX);
	}

	/**
	 * Sets the ns_prefix of this MetadataPropertyType.
	 * @param ns_prefix
	 */
	function set_ns_prefix($ns_prefix)
	{
		$this->set_default_property(self :: PROPERTY_NS_PREFIX, $ns_prefix);
	}

	/**
	 * Returns the value of this MetadataPropertyType.
	 * @return the value.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the value of this MetadataPropertyType.
	 * @param value
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>