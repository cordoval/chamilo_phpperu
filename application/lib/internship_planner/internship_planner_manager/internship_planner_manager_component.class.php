<?php

/**
 * @package application.lib.internship_planner.internship_planner_manager
 * Basic functionality of a component to talk with the internship_planner application
 *
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
abstract class InternshipPlannerManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param InternshipPlanner $internship_planner The internship_planner which
	 * provides this component
	 */
	function InternshipPlannerManagerComponent($internship_planner)
	{
		parent :: __construct($internship_planner);
	}

	//Data Retrieval

	function count_categories($condition)
	{
		return $this->get_parent()->count_categories($condition);
	}

	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_categories($condition, $offset, $count, $order_property);
	}

 	function retrieve_category($id)
	{
		return $this->get_parent()->retrieve_category($id);
	}

	function count_locations($condition)
	{
		return $this->get_parent()->count_locations($condition);
	}

	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
	}

 	function retrieve_location($id)
	{
		return $this->get_parent()->retrieve_location($id);
	}

	function count_location_groups($condition)
	{
		return $this->get_parent()->count_location_groups($condition);
	}

	function retrieve_location_groups($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_location_groups($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_group($id)
	{
		return $this->get_parent()->retrieve_location_group($id);
	}

	function count_location_rel_categories($condition)
	{
		return $this->get_parent()->count_location_rel_categories($condition);
	}

	function retrieve_location_rel_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_location_rel_categories($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_category($id)
	{
		return $this->get_parent()->retrieve_location_rel_category($id);
	}

	function count_location_rel_mentors($condition)
	{
		return $this->get_parent()->count_location_rel_mentors($condition);
	}

	function retrieve_location_rel_mentors($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_location_rel_mentors($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_mentor($id)
	{
		return $this->get_parent()->retrieve_location_rel_mentor($id);
	}

	function count_location_rel_moments($condition)
	{
		return $this->get_parent()->count_location_rel_moments($condition);
	}

	function retrieve_location_rel_moments($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_location_rel_moments($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_moment($id)
	{
		return $this->get_parent()->retrieve_location_rel_moment($id);
	}

	function count_location_rel_types($condition)
	{
		return $this->get_parent()->count_location_rel_types($condition);
	}

	function retrieve_location_rel_types($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_location_rel_types($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_type($id)
	{
		return $this->get_parent()->retrieve_location_rel_type($id);
	}

	function count_mentors($condition)
	{
		return $this->get_parent()->count_mentors($condition);
	}

	function retrieve_mentors($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_mentors($condition, $offset, $count, $order_property);
	}

 	function retrieve_mentor($id)
	{
		return $this->get_parent()->retrieve_mentor($id);
	}

	function count_moments($condition)
	{
		return $this->get_parent()->count_moments($condition);
	}

	function retrieve_moments($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_moments($condition, $offset, $count, $order_property);
	}

 	function retrieve_moment($id)
	{
		return $this->get_parent()->retrieve_moment($id);
	}

	function count_periods($condition)
	{
		return $this->get_parent()->count_periods($condition);
	}

	function retrieve_periods($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_periods($condition, $offset, $count, $order_property);
	}

 	function retrieve_period($id)
	{
		return $this->get_parent()->retrieve_period($id);
	}

	function count_places($condition)
	{
		return $this->get_parent()->count_places($condition);
	}

	function retrieve_places($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_places($condition, $offset, $count, $order_property);
	}

 	function retrieve_place($id)
	{
		return $this->get_parent()->retrieve_place($id);
	}

	// Url Creation

	function get_create_category_url()
	{
		return $this->get_parent()->get_create_category_url();
	}

	function get_update_category_url($category)
	{
		return $this->get_parent()->get_update_category_url($category);
	}

 	function get_delete_category_url($category)
	{
		return $this->get_parent()->get_delete_category_url($category);
	}

	function get_browse_categories_url()
	{
		return $this->get_parent()->get_browse_categories_url();
	}

	function get_create_location_url()
	{
		return $this->get_parent()->get_create_location_url();
	}

	function get_update_location_url($location)
	{
		return $this->get_parent()->get_update_location_url($location);
	}

 	function get_delete_location_url($location)
	{
		return $this->get_parent()->get_delete_location_url($location);
	}

	function get_browse_locations_url()
	{
		return $this->get_parent()->get_browse_locations_url();
	}

	function get_create_location_group_url()
	{
		return $this->get_parent()->get_create_location_group_url();
	}

	function get_update_location_group_url($location_group)
	{
		return $this->get_parent()->get_update_location_group_url($location_group);
	}

 	function get_delete_location_group_url($location_group)
	{
		return $this->get_parent()->get_delete_location_group_url($location_group);
	}

	function get_browse_location_groups_url()
	{
		return $this->get_parent()->get_browse_location_groups_url();
	}

	function get_create_location_rel_category_url()
	{
		return $this->get_parent()->get_create_location_rel_category_url();
	}

	function get_update_location_rel_category_url($location_rel_category)
	{
		return $this->get_parent()->get_update_location_rel_category_url($location_rel_category);
	}

 	function get_delete_location_rel_category_url($location_rel_category)
	{
		return $this->get_parent()->get_delete_location_rel_category_url($location_rel_category);
	}

	function get_browse_location_rel_categories_url()
	{
		return $this->get_parent()->get_browse_location_rel_categories_url();
	}

	function get_create_location_rel_mentor_url()
	{
		return $this->get_parent()->get_create_location_rel_mentor_url();
	}

	function get_update_location_rel_mentor_url($location_rel_mentor)
	{
		return $this->get_parent()->get_update_location_rel_mentor_url($location_rel_mentor);
	}

 	function get_delete_location_rel_mentor_url($location_rel_mentor)
	{
		return $this->get_parent()->get_delete_location_rel_mentor_url($location_rel_mentor);
	}

	function get_browse_location_rel_mentors_url()
	{
		return $this->get_parent()->get_browse_location_rel_mentors_url();
	}

	function get_create_location_rel_moment_url()
	{
		return $this->get_parent()->get_create_location_rel_moment_url();
	}

	function get_update_location_rel_moment_url($location_rel_moment)
	{
		return $this->get_parent()->get_update_location_rel_moment_url($location_rel_moment);
	}

 	function get_delete_location_rel_moment_url($location_rel_moment)
	{
		return $this->get_parent()->get_delete_location_rel_moment_url($location_rel_moment);
	}

	function get_browse_location_rel_moments_url()
	{
		return $this->get_parent()->get_browse_location_rel_moments_url();
	}

	function get_create_location_rel_type_url()
	{
		return $this->get_parent()->get_create_location_rel_type_url();
	}

	function get_update_location_rel_type_url($location_rel_type)
	{
		return $this->get_parent()->get_update_location_rel_type_url($location_rel_type);
	}

 	function get_delete_location_rel_type_url($location_rel_type)
	{
		return $this->get_parent()->get_delete_location_rel_type_url($location_rel_type);
	}

	function get_browse_location_rel_types_url()
	{
		return $this->get_parent()->get_browse_location_rel_types_url();
	}

	function get_create_mentor_url()
	{
		return $this->get_parent()->get_create_mentor_url();
	}

	function get_update_mentor_url($mentor)
	{
		return $this->get_parent()->get_update_mentor_url($mentor);
	}

 	function get_delete_mentor_url($mentor)
	{
		return $this->get_parent()->get_delete_mentor_url($mentor);
	}

	function get_browse_mentors_url()
	{
		return $this->get_parent()->get_browse_mentors_url();
	}

	function get_create_moment_url()
	{
		return $this->get_parent()->get_create_moment_url();
	}

	function get_update_moment_url($moment)
	{
		return $this->get_parent()->get_update_moment_url($moment);
	}

 	function get_delete_moment_url($moment)
	{
		return $this->get_parent()->get_delete_moment_url($moment);
	}

	function get_browse_moments_url()
	{
		return $this->get_parent()->get_browse_moments_url();
	}

	function get_create_period_url()
	{
		return $this->get_parent()->get_create_period_url();
	}

	function get_update_period_url($period)
	{
		return $this->get_parent()->get_update_period_url($period);
	}

 	function get_delete_period_url($period)
	{
		return $this->get_parent()->get_delete_period_url($period);
	}

	function get_browse_periods_url()
	{
		return $this->get_parent()->get_browse_periods_url();
	}

	function get_create_place_url()
	{
		return $this->get_parent()->get_create_place_url();
	}

	function get_update_place_url($place)
	{
		return $this->get_parent()->get_update_place_url($place);
	}

 	function get_delete_place_url($place)
	{
		return $this->get_parent()->get_delete_place_url($place);
	}

	function get_browse_places_url()
	{
		return $this->get_parent()->get_browse_places_url();
	}


	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}
}
?>