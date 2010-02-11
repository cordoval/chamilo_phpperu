<?php
require_once dirname(__FILE__).'/gradebook_data_manager.class.php';
/**
 * @package gradebook
 */
/**
 *	@author Eduard Vossen
 *
 */

class Gradebook
{
	const CLASS_NAME = __CLASS__;

	const PROPERTY_ID = 'id';
	const PROPERTY_OWNER_ID = 'owner_id';
	const PROPERTY_CREATED = 'created';
	const PROPERTY_START = 'start';
	const PROPERTY_END = 'end';
	const PROPERTY_SCALE = 'scale';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';

	
	private $defaultProperties;
	
	function Gradebook($id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,
		self :: PROPERTY_CREATED,
		self ::PROPERTY_START,
		self ::PROPERTY_END,
		self :: PROPERTY_OWNER_ID, self :: PROPERTY_DESCRIPTION,
		self :: PROPERTY_NAME,
		self :: PROPERTY_SCALE);
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	function get_owner_id()
	{
		return $this->get_default_property(self :: PROPERTY_OWNER_ID);
	}

	function set_owner_id($owner_id)
	{
		$this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
	}

	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}

	function get_created()
	{
		return $this->get_default_property(self :: PROPERTY_CREATED);
	}

	function get_start()
	{
		return $this->get_default_property(self :: PROPERTY_START);
	}

	function get_end()
	{
		return $this->get_default_property(self :: PROPERTY_END);
	}

	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	function set_created($created)
	{
		$this->set_default_property(self :: PROPERTY_CREATED, $created);
	}

	function set_start($start)
	{
		$this->set_default_property(self :: PROPERTY_START, $start);
	}

	function set_end($end)
	{
		$this->set_default_property(self :: PROPERTY_END, $end);
	}

	function get_scale()
	{
		return $this->get_default_property(self :: PROPERTY_SCALE);
	}

	function set_scale($scale)
	{
		$this->set_default_property(self :: PROPERTY_SCALE, $scale);
	}

	function delete()
	{
		return GradebookDataManager :: get_instance()->delete_gradebook($this);
	}

	function create()
	{
		$dm = GradebookDataManager :: get_instance();
		$now = time();
		$this->set_created($now);
		//$this->set_id($dm->get_next_gradebook_id());
		return $dm->create_gradebook($this);
	}

	function update()
	{
		$dm = GradebookDataManager :: get_instance();
		$success = $dm->update_gradebook($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}

	function truncate()
	{
		return GradebookDataManager :: get_instance()->truncate_gradebook($this);
	}
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

	function to_array()
	{
		return $this->defaultProperties;
	}
}
?>