<?php
/**
 * This abstract class provides the necessary functionality to connect a
 * gradebook to a storage system.
 */
abstract class GradebookDatamanager {

	/**
	 * Instance of the class, for the singleton pattern.
	 */
	private static $instance;
	/**
	 * Constructor. Initializes the data manager.
	 */
	protected function GradebookDatamanager()
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

	abstract function get_next_gradebook_id();

	abstract function delete_gradebook($gradebook);

	abstract function update_gradebook($gradebook);

	abstract function create_gradebook($gradebook);
	
	abstract function truncate_gradebook($id);
	
	abstract function count_gradebooks($conditions = null);

	abstract function retrieve_gradebook($id);

	abstract function retrieve_gradebooks($condition = null, $offset = null, $count = null, $order_property = null);


	//gradebook_items rel user

	abstract function create_gradebook_rel_user($gradebookreluser);

	abstract function delete_gradebook_rel_user($gradebookreluser);

	abstract function update_gradebook_rel_user($gradebookreluser);
	
	abstract function count_gradebook_rel_users($conditions = null);

	abstract function retrieve_gradebook_rel_user($user_id, $gradebook_id);

	abstract function retrieve_gradebook_rel_users($condition = null, $offset = null, $count = null, $order_property = null);
	
}
?>