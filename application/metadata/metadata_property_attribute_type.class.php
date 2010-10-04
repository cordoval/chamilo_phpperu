<?php 
/**
 * metadata
 */

/**
 * This class describes a MetadataPropertyAttributeType data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyAttributeType extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * MetadataPropertyAttributeType properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NS_PREFIX = 'ns_prefix';
	const PROPERTY_NAME = 'name';
	const PROPERTY_VALUE = 'value';
	const PROPERTY_VALUE_TYPE = 'value_type';

        const VALUE_TYPE_NONE = '0';
        const VALUE_TYPE_ID = '1';
        const VALUE_TYPE_VALUE = '2';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NS_PREFIX, self :: PROPERTY_NAME, self :: PROPERTY_VALUE, self :: PROPERTY_VALUE_TYPE);
	}

	function get_data_manager()
	{
		return MetadataDataManager :: get_instance();
	}

	/**
	 * Returns the id of this MetadataPropertyAttributeType.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this MetadataPropertyAttributeType.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the ns_prefix of this MetadataPropertyAttributeType.
	 * @return the ns_prefix.
	 */
	function get_ns_prefix()
	{
		return $this->get_default_property(self :: PROPERTY_NS_PREFIX);
	}

	/**
	 * Sets the ns_prefix of this MetadataPropertyAttributeType.
	 * @param ns_prefix
	 */
	function set_ns_prefix($ns_prefix)
	{
		$this->set_default_property(self :: PROPERTY_NS_PREFIX, $ns_prefix);
	}

	/**
	 * Returns the name of this MetadataPropertyAttributeType.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this MetadataPropertyAttributeType.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the value of this MetadataPropertyAttributeType.
	 * @return the value.
	 */
	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}

	/**
	 * Sets the value of this MetadataPropertyAttributeType.
	 * @param value
	 */
	function set_value($value)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $value);
	}

	/**
	 * Returns the value_type of this MetadataPropertyAttributeType.
	 * @return the value_type.
	 */
	function get_value_type()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE_TYPE);
	}

	/**
	 * Sets the value_type of this MetadataPropertyAttributeType.
	 * @param value_type
	 */
	function set_value_type($value_type)
	{
		$this->set_default_property(self :: PROPERTY_VALUE_TYPE, $value_type);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

        function render_name()
        {
            $pref = $this->get_ns_prefix();
            $prefix = (empty($pref)) ? '' : $this->get_ns_prefix() . ':';
            return $prefix . $this->get_name();
        }
}

?>