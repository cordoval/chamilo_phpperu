<?php 
/**
 * cda
 */

/**
 * This class describes a LanguagePack data object
 * @author Sven Vanpoucke
 * @author 
 */
class LanguagePack extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * LanguagePack properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_TYPE = 'type';
	
	const TYPE_CORE = 1;
	const TYPE_APPLICATION = 2;

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_TYPE);
	}

	function get_data_manager()
	{
		return CdaDataManager :: get_instance();
	}

	/**
	 * Returns the id of this LanguagePack.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this LanguagePack.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this LanguagePack.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this LanguagePack.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the type of this LanguagePack.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Sets the type of this LanguagePack.
	 * @param type
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function get_type_name()
	{
		switch($this->get_type())
		{
			case LanguagePack :: TYPE_CORE:
				return Translation :: get('Core');
			default:
				return Translation :: get('Application');	
		}
	}
}

?>