<?php 
/**
 * This class describes a Competency data object
 * 
 * @author Nick Van Loocke
 */
class Competency extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Competency properties
	 */
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION);
	}

	function get_data_manager()
	{
		return CbaDataManager :: get_instance();
	}

	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}

	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>