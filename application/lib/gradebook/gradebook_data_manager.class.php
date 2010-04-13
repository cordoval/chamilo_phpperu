<?php
/**
 * This abstract class provides the necessary functionality to connect a
 * gradebook to a storage system.
 */
abstract class GradebookDataManager {

	/**
	 * Instance of the class, for the singleton pattern.
	 */
	private static $instance;
	/**
	 * Constructor. Initializes the data manager.
	 */
	protected function GradebookDataManager()
	{
		$this->initialize();
	}
	/**
	 * Creates the shared instance of the configured data manager if
	 * necessary and returns it. Uses a factory pattern.
	 * @return GradebookManager The instance.
	 */
	static function get_instance()

	{

		if (!isset (self :: $instance))
		{

			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');

			require_once dirname(__FILE__).'/data_manager/'.Utilities::camelcase_to_underscores($type).'.class.php';
			$class = $type.'GradebookDataManager';

			self :: $instance = new $class ();

		}

		return self :: $instance;
	}

	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();
	
	//gradebook_items
	
	//gradebook evaluation format items
	
	abstract function create_format($evaluation_format);
	
	abstract function update_format($evaluation_format);
	
	abstract function retrieve_all_active_evaluation_formats();
	
	abstract function retrieve_evaluation_formats();
	
	abstract function count_evaluation_formats();
	
	abstract function retrieve_evaluation_format($id);
	
	// internal items
	
	abstract function retrieve_internal_item_by_publication($application, $publication_id);
	
	abstract function create_internal_item($internal_item);
	
	abstract function delete_internal_item($internal_item);
	
	// internal item instance
	abstract function delete_internal_item_instance($internal_item_instance);
	
	abstract function retrieve_evaluation_ids_by_internal_item_id($internal_item_id);
	
	abstract function update_internal_item_instance($internal_item_instance);
	
	// evaluation
	abstract function move_internal_to_external($application, $publication);
	
	abstract function retrieve_evaluation_ids_by_publication($application, $publication_id);
	
	abstract function create_evaluation($evaluation);	
	
	abstract function delete_evaluation($evaluation);
	
	abstract function update_evaluation($evaluation);
	
	abstract function retrieve_all_evaluations_on_publication($publication_id, $offset = null, $count = null, $order_property = null);
//	
//	abstract function retrieve_all_evaluation_formats();
//	
//	abstract function retrieve_all_active_evaluation_formats();

	/*abstract function get_next_gradebook_id();

	abstract function delete_gradebook($gradebook);

	abstract function update_gradebook($gradebook);

	abstract function create_gradebook($gradebook);
	
	abstract function truncate_gradebook($id);
	
	abstract function count_gradebooks($conditions = null);*/

	abstract function retrieve_evaluation($id);

	abstract function retrieve_evaluations($condition = null, $offset = null, $count = null, $order_property = null);


	//gradebook_items rel user

	//gradebook grade evaluation
	
	abstract function retrieve_grade_evaluation($id);
	
	abstract function delete_grade_evaluation($grade_evaluation);
	
	abstract function update_grade_evaluation($grade_evaluation);
	
	/*
	abstract function create_gradebook_rel_user($gradebookreluser);

	abstract function delete_gradebook_rel_user($gradebookreluser);

	abstract function update_gradebook_rel_user($gradebookreluser);
	
	abstract function count_gradebook_rel_users($conditions = null);

	abstract function retrieve_gradebook_rel_user($user_id, $gradebook_id);

	abstract function retrieve_gradebook_rel_users($condition = null, $offset = null, $count = null, $order_property = null);
	*/
}
?>