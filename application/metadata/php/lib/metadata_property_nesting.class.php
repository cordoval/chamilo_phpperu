<?php namespace application\metadata;
use common\libraries\DataClass;
/**
 * metadata
 */

/**
 * This class describes a MetadataPropertyNesting data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyNesting extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * MetadataPropertyNesting properties
	 */
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_CHILD_ID = 'child_id';
	const PROPERTY_CHILD_TYPE = 'child_type';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PARENT_ID, self :: PROPERTY_CHILD_ID, self :: PROPERTY_CHILD_TYPE);
	}

	function get_data_manager()
	{
		return MetadataDataManager :: get_instance();
	}

	/**
	 * Returns the parent_id of this MetadataPropertyNesting.
	 * @return the parent_id.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}

	/**
	 * Sets the parent_id of this MetadataPropertyNesting.
	 * @param parent_id
	 */
	function set_parent_id($parent_id)
	{
		$this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
	}

	/**
	 * Returns the child_id of this MetadataPropertyNesting.
	 * @return the child_id.
	 */
	function get_child_id()
	{
		return $this->get_default_property(self :: PROPERTY_CHILD_ID);
	}

	/**
	 * Sets the child_id of this MetadataPropertyNesting.
	 * @param child_id
	 */
	function set_child_id($child_id)
	{
		$this->set_default_property(self :: PROPERTY_CHILD_ID, $child_id);
	}

	/**
	 * Returns the child_type of this MetadataPropertyNesting.
	 * @return the child_type.
	 */
	function get_child_type()
	{
		return $this->get_default_property(self :: PROPERTY_CHILD_TYPE);
	}

	/**
	 * Sets the child_type of this MetadataPropertyNesting.
	 * @param child_type
	 */
	function set_child_type($child_type)
	{
		$this->set_default_property(self :: PROPERTY_CHILD_TYPE, $child_type);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}
}

?>