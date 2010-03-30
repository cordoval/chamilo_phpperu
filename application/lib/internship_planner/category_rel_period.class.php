<?php 
/**
 * This class describes a InternshipCategoryRelPeriod data object
 * @author Sven Vanhoecke
 */
class InternshipPlannerCategoryRelPeriod extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipPlannerCategoryRelPeriod properties
	 */
	const PROPERTY_PERIOD_ID = 'period_id';
	const PROPERTY_CATEGORY_ID = 'category_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PERIOD_ID, self :: PROPERTY_CATEGORY_ID);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the period_id of this InternshipPlannerCategoryRelPeriod.
	 * @return the period_id.
	 */
	function get_period_id()
	{
		return $this->get_default_property(self :: PROPERTY_PERIOD_ID);
	}

	/**
	 * Sets the period_id of this InternshipPlannerCategoryRelPeriod.
	 * @param period_id
	 */
	function set_period_id($period_id)
	{
		$this->set_default_property(self :: PROPERTY_PERIOD_ID, $period_id);
	}

	/**
	 * Returns the category_id of this InternshipPlannerCategoryRelPeriod.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}

	/**
	 * Sets the category_id of this InternshipPlannerCategoryRelPeriod.
	 * @param category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}


	static function get_table_name()
	{
		return 'category_rel_period';
//		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>