<?php

require_once dirname(__FILE__).'/gradebook_data_manager.class.php';
/**
 * @package gradebook
 */

class GradebookRelUser
{
	const CLASS_NAME = __CLASS__;
	const PROPERTY_GRADEBOOK_ID = 'gradebook_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_SCORE ='score';

	private $defaultProperties;

	function GradebookRelUser($gradebook_id = 0, $user_id = 0, $defaultProperties = array())
	{
		$this->set_gradebook_id($gradebook_id);
		$this->set_user_id($user_id);
	}
	
	function get_gradebook_id()
	{
		return $this->get_default_property(self :: PROPERTY_GRADEBOOK_ID);
	}
	
	function set_gradebook_id($gradebook_id)
	{
		$this->set_default_property(self :: PROPERTY_GRADEBOOK_ID, $gradebook_id);
	}
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}	
	
	function get_score()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE);
	}
	
	function set_score($score)
	{
		$this->set_default_property(self :: PROPERTY_SCORE, $score);
	}	
		
	
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this group.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
		
	function get_default_property_names()
	{
		return array (self :: PROPERTY_GRADEBOOK_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_SCORE);
	}
	
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	
	function delete()
	{
		return GradebookDataManager :: get_instance()->delete_gradebook_rel_user($this);
	}
	
	function update(){
		return GradebookDataManager :: get_instance()->update_gradebook_rel_user($this);
		
	}
	
	function create()
	{
		$gdm = GradebookDataManager :: get_instance();
		return $gdm->create_gradebook_rel_user($this);
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