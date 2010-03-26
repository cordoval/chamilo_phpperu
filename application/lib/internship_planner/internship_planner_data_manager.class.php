<?php
/**
 * This is a skeleton for a data manager for the InternshipPlanner Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
abstract class InternshipPlannerDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function InternshipPlannerDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return InternshipPlannerDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'InternshipPlannerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    //	abstract function get_next_category_id();
    //	abstract function create_category($category);
    //	abstract function update_category($category);
    //	abstract function delete_category($category);
    //	abstract function count_categories($conditions = null);
    //	abstract function retrieve_category($id);
    //	abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);
    

    abstract function create_internship_location($location);

    abstract function update_internship_location($location);

    abstract function delete_internship_location($location);

    abstract function count_locations($conditions = null);

    abstract function retrieve_location($id);

    abstract function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_internship_organisation($organisation);

    abstract function update_internship_organisation($organisation);

    abstract function delete_internship_organisation($organisation);

    abstract function count_organisations($conditions = null);

    abstract function retrieve_organisation($id);

    abstract function retrieve_organisations($condition = null, $offset = null, $count = null, $order_property = null);

    
    
    abstract function delete_category($category);

    abstract function delete_category_rel_location($categoryrellocation);

    abstract function update_category($category);

    abstract function create_category($category);

    abstract function create_category_rel_location($categoryrellocation);

    abstract function count_categories($conditions = null);

    abstract function count_category_rel_locations($conditions = null);

    abstract function retrieve_category($id);

    abstract function truncate_category($id);

    abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_category_rel_location($location_id, $category_id);

    abstract function retrieve_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_location_categories($location_id);

    abstract function add_nested_values($previous_visited, $number_of_elements = 1);

    abstract function delete_nested_values($category);

    abstract function move_category($category, $new_parent_id, $new_previous_id = 0);
    
//	abstract function get_next_location_category_id();
//	abstract function create_location_category($location_category);
//	abstract function update_location_category($location_category);
//	abstract function delete_location_category($location_category);
//	abstract function count_location_categories($conditions = null);
//	abstract function retrieve_location_category($id);
//	abstract function retrieve_location_categories($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_location_rel_category_id();
//	abstract function create_location_rel_category($location_rel_category);
//	abstract function update_location_rel_category($location_rel_category);
//	abstract function delete_location_rel_category($location_rel_category);
//	abstract function count_location_rel_categories($conditions = null);
//	abstract function retrieve_location_rel_category($id);
//	abstract function retrieve_location_rel_categories($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_location_rel_mentor_id();
//	abstract function create_location_rel_mentor($location_rel_mentor);
//	abstract function update_location_rel_mentor($location_rel_mentor);
//	abstract function delete_location_rel_mentor($location_rel_mentor);
//	abstract function count_location_rel_mentors($conditions = null);
//	abstract function retrieve_location_rel_mentor($id);
//	abstract function retrieve_location_rel_mentors($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_location_rel_moment_id();
//	abstract function create_location_rel_moment($location_rel_moment);
//	abstract function update_location_rel_moment($location_rel_moment);
//	abstract function delete_location_rel_moment($location_rel_moment);
//	abstract function count_location_rel_moments($conditions = null);
//	abstract function retrieve_location_rel_moment($id);
//	abstract function retrieve_location_rel_moments($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_location_rel_type_id();
//	abstract function create_location_rel_type($location_rel_type);
//	abstract function update_location_rel_type($location_rel_type);
//	abstract function delete_location_rel_type($location_rel_type);
//	abstract function count_location_rel_types($conditions = null);
//	abstract function retrieve_location_rel_type($id);
//	abstract function retrieve_location_rel_types($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_mentor_id();
//	abstract function create_mentor($mentor);
//	abstract function update_mentor($mentor);
//	abstract function delete_mentor($mentor);
//	abstract function count_mentors($conditions = null);
//	abstract function retrieve_mentor($id);
//	abstract function retrieve_mentors($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_moment_id();
//	abstract function create_moment($moment);
//	abstract function update_moment($moment);
//	abstract function delete_moment($moment);
//	abstract function count_moments($conditions = null);
//	abstract function retrieve_moment($id);
//	abstract function retrieve_moments($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_period_id();
//	abstract function create_period($period);
//	abstract function update_period($period);
//	abstract function delete_period($period);
//	abstract function count_periods($conditions = null);
//	abstract function retrieve_period($id);
//	abstract function retrieve_periods($condition = null, $offset = null, $count = null, $order_property = null);
//
//	abstract function get_next_place_id();
//	abstract function create_place($place);
//	abstract function update_place($place);
//	abstract function delete_place($place);
//	abstract function count_places($conditions = null);
//	abstract function retrieve_place($id);
//	abstract function retrieve_places($condition = null, $offset = null, $count = null, $order_property = null);


}
?>