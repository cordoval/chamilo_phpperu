<?php 
/**
 * internship_planner
 */

/**
 * This class describes a InternshipPlannerLocationRelCategory data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerCategoryRelLocation extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipCategoryRelLocation properties
	 */
	
	const PROPERTY_LOCATION_ID = 'location_id';
	const PROPERTY_CATEGORY_ID = 'category_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_LOCATION_ID, self :: PROPERTY_CATEGORY_ID);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the location_id of this InternshipPlannerLocationRelCategory.
	 * @return the location_id.
	 */
	function get_location_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
	}

	/**
	 * Sets the location_id of this InternshipPlannerLocationRelCategory.
	 * @param location_id
	 */
	function set_location_id($location_id)
	{
		$this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
	}

	/**
	 * Returns the category_id of this InternshipPlannerLocationRelCategory.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}

	/**
	 * Sets the category_id of this InternshipPlannerLocationRelCategory.
	 * @param category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}


	static function get_table_name()
	{
		return 'category_rel_location';
//		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>