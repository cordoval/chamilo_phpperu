<?php
/**
 * This is a skeleton for a data manager for the InternshipOrganizer Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
abstract class InternshipOrganizerDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function InternshipOrganizerDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return InternshipOrganizerDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'InternshipOrganizerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function get_database();
    
    abstract function create_internship_organizer_location($location);

    abstract function update_internship_organizer_location($location);

    abstract function delete_internship_organizer_location($location);

    abstract function count_locations($conditions = null);

    abstract function retrieve_location($id);

    abstract function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_internship_organizer_organisation($organisation);

    abstract function update_internship_organizer_organisation($organisation);

    abstract function delete_internship_organizer_organisation($organisation);

    abstract function count_organisations($conditions = null);

    abstract function retrieve_organisation($id);

    abstract function retrieve_organisations($condition = null, $offset = null, $count = null, $order_property = null);

    
    
    abstract function delete_internship_organizer_category($category);

    abstract function delete_category_rel_location($categoryrellocation);

    abstract function update_internship_organizer_category($category);

    abstract function create_internship_organizer_category($category);

    abstract function create_internship_organizer_category_rel_location($categoryrellocation);

    abstract function count_categories($conditions = null);

    abstract function count_category_rel_locations($conditions = null);

    abstract function retrieve_internship_organizer_category($id);

    abstract function truncate_category($id);

    abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);
	
    abstract function retrieve_root_category();
    
    abstract function retrieve_category_rel_location($location_id, $category_id);

    abstract function retrieve_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function add_internship_organizer_category_nested_values($node, $previous_visited, $number_of_elements = 1, $condition);

    abstract function delete_internship_organizer_category_nested_values($node, $previous_visited, $number_of_elements, $condition);

    abstract function move_internship_organizer_category($category, $new_parent_id, $new_previous_id = 0, $condition);
	
    
    abstract function create_internship_organizer_moment($moment);

    abstract function update_internship_organizer_moment($moment);

    abstract function delete_internship_organizer_moment($moment);

    abstract function count_moments($conditions = null);

    abstract function retrieve_moment($id);

    abstract function retrieve_moments($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_internship_organizer_agreement($organisation);

    abstract function update_internship_organizer_agreement($organisation);

    abstract function delete_internship_organizer_agreement($organisation);

    abstract function count_agreements($conditions = null);

    abstract function retrieve_agreement($id);

    abstract function retrieve_agreements($condition = null, $offset = null, $count = null, $order_property = null);
    
    

    abstract function delete_internship_organizer_region($region);

	abstract function update_internship_organizer_region($region);

    abstract function create_internship_organizer_region($region);

    abstract function count_regions($conditions = null);

    abstract function retrieve_internship_organizer_region($id);

    abstract function truncate_region($id);

    abstract function retrieve_regions($condition = null, $offset = null, $count = null, $order_property = null);
	
    abstract function retrieve_root_region();
    
    abstract function add_internship_organizer_region_nested_values($node, $previous_visited, $number_of_elements = 1, $condition);

    abstract function delete_internship_organizer_region_nested_values($node, $previous_visited, $number_of_elements, $condition);	
    
}
?>