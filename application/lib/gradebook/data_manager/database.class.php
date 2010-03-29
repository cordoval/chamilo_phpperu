<?php
require_once dirname(__FILE__).'/../gradebook_data_manager.class.php';
require_once dirname(__FILE__).'/../internal_item.class.php';
require_once dirname(__FILE__).'/../external_item.class.php';
require_once dirname(__FILE__).'/../evaluation.class.php';
require_once dirname(__FILE__).'/../format.class.php';
require_once dirname(__FILE__).'/../grade_evaluation.class.php';
require_once dirname(__FILE__).'/../internal_item_instance.class.php';
require_once dirname(__FILE__).'/../external_item_instance.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

class DatabaseGradebookDataManager extends GradebookDatamanager
{

	private $database;

	function initialize()
	{   
		$aliases = array();
		$aliases[InternalItem :: get_table_name()] = 'inem';
		$aliases[ExternalItem :: get_table_name()] = 'exem';
		$aliases[Evaluation :: get_table_name()] = 'evon';
		$aliases[Format :: get_table_name()] = 'foat';
		$aliases[GradeEvaluation :: get_table_name()] = 'gron';
		$aliases[InternalItemInstance :: get_table_name()] = 'ince';
		$aliases[ExternalItemInstance :: get_table_name()] = 'exce';
		
		$this->database = new Database($aliases);
		$this->database->set_prefix('gradebook_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name,$properties,$indexes);
	}
	
// gradebook evaluation format items
	function create_format($evaluation_format)
	{
		return $this->database->create($evaluation_format);
	}
//	
//	function retrieve_all_evaluation_formats($condition = null, $offset = null, $count = null, $order_property = null)
//	{
//		return $this->database->retrieve_objects(GradebookEvaluationFormat :: get_table_name(), $condition, $offset, $count, $order_property);
//	}
//	
	function retrieve_all_active_evaluation_formats()
	{
		$condition = new EqualityCondition(Format :: PROPERTY_ACTIVE, Format :: EVALUATION_FORMAT_ACTIVE);
		return $this->database->retrieve_objects(Format :: get_table_name(), $condition);
	}

	//gradebook_items

	/*function get_next_gradebook_id(){
		$id = $this->database->get_next_id(Gradebook :: get_table_name());
		return $id;
	}

	function delete_gradebook($gradebook){
		$condition = new EqualityCondition(Gradebook :: PROPERTY_ID, $gradebook->get_id());
		$bool = $this->database->delete($gradebook->get_table_name(), $condition);

		$condition_rel_users = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $gradebook->get_id());
		$gradebook_rel_users = $this->retrieve_gradebook_rel_users($condition_rel_users);
		while($gradebook_rel_user = $gradebook_rel_users->next_result())
		{
			$bool = $bool & $this->delete_gradebook_rel_user($gradebook_rel_user);
		}
		return $bool;
	}

	function update_gradebook($gradebook){
		$condition = new EqualityCondition(Gradebook :: PROPERTY_ID, $gradebook->get_id());
		return $this->database->update($gradebook, $condition);;
	}

	function create_gradebook($gradebook){
		return $this->database->create($gradebook);
	}

	function truncate_gradebook($gradebook)
	{
		$condition = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $gradebook->get_id());
		return $this->database->delete(GradebookRelUser :: get_table_name(), $condition);
	}

	function count_gradebooks($conditions = null){
		return $this->database->count_objects(Gradebook :: get_table_name(), $condition);
	}

	function retrieve_gradebook($id){
		$condition = new EqualityCondition(Gradebook :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Gradebook :: get_table_name(), $condition);
	}

	function retrieve_gradebooks($condition = null, $offset = null, $count = null, $order_property = null){
		return $this->database->retrieve_objects(Gradebook :: get_table_name(), $condition, $offset, $count, $order_property);
	}


	//gradebook_items rel user

	function create_gradebook_rel_user($gradebookreluser){
		return $this->database->create($gradebookreluser);
	}

	function delete_gradebook_rel_user($gradebookreluser){
		$conditions = array();
		$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $gradebookreluser->get_gradebook_id());
		$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_USER_ID, $gradebookreluser->get_user_id());
		$condition = new AndCondition($conditions);
		return $this->database->delete(GradebookRelUser :: get_table_name(), $condition);
	}

	function update_gradebook_rel_user($gradebookreluser){

		

		$conditions = array();
		$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $gradebookreluser->get_gradebook_id());
		$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_USER_ID, $gradebookreluser->get_user_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($gradebookreluser, $condition);;
	}

	function count_gradebook_rel_users($condition = null){
		return $this->database->count_objects(GradebookRelUser :: get_table_name(), $condition);
	}

	function retrieve_gradebook_rel_user($user_id, $gradebook_id){
		$conditions = array();
		$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $gradebook_id);
		$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_USER_ID, $user_id);
		$condition = new AndCondition($conditions);
		return $this->database->retrieve_object(GradebookRelUser :: get_table_name(), $condition);
	}

	function retrieve_gradebook_rel_users($condition = null, $offset = null, $count = null, $order_property = null){
		return $this->database->retrieve_objects(GradebookRelUser :: get_table_name(), $condition, $offset, $count, $order_property);

	}

	function get_database()
	{
		return $this->database;
	}*/
}
?>