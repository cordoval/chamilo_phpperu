<?php 
/**
 * streaming_video
 */

/**
 * This class describes a Parameter data object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class Parameter extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Parameter properties
	 */
	const PROPERTY_NAME = 'name';
	const PROPERTY_VALUE = 'value';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_NAME, self :: PROPERTY_VALUE);
	}

	function get_data_manager()
	{
		return StreamingVideoDataManager :: get_instance();
	}

	/**
	 * Returns the name of this Parameter.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Parameter.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the value of this Parameter.
	 * @return the value.
	 */
	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}

	/**
	 * Sets the value of this Parameter.
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
}

?>