<?php 
/**
 * internship_planner
 */
require_once dirname(__FILE__) . 'internship_planner_data_manager.class.php';
/**
 * This class describes a Category data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class Category extends NestedTreeNode
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Category properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(
        		array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION));
	}

	/**
	 * Returns the id of this Category.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Category.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this Category.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Category.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the description of this Category.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this Category.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function get_data_manager() 
	{
		return InternshipPlannerDataManager :: get_instance();
	}
	
	function create()
	{
		if(!$this->get_parent_id())
		{
			$root_category = InternshipPlannerDataManager :: get_instance()->retrieve_category_root($this->id());
			if($root_category)
			{
				$this->set_parent_id($root_category->get_id());
			}
		}
		
		return parent :: create();
	}
}

?>