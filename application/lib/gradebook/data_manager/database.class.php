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

class DatabaseGradebookDataManager extends GradebookDataManager
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
	
	function get_database()
	{
		return $this->database;
	}
	
// gradebook evaluation format items
	function create_format($evaluation_format)
	{
		return $this->database->create($evaluation_format);
	}
	
	function update_format($evaluation_format)
	{
		$condition = new EqualityCondition(Format :: PROPERTY_ID, $evaluation_format->get_id());
		return $this->database->update($evaluation_format, $condition);
	}
	
	function retrieve_all_active_evaluation_formats()
	{
		$condition = new EqualityCondition(Format :: PROPERTY_ACTIVE, Format :: EVALUATION_FORMAT_ACTIVE);
		return $this->database->retrieve_objects(Format :: get_table_name(), $condition);
	}

	function retrieve_evaluation_formats($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Format :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function count_evaluation_formats()
	{
		return $this->database->count_objects(Format :: get_table_name());
	}
	
	function retrieve_evaluation_format($id)
	{
		$condition = new EqualityCondition(Format :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Format :: get_table_name(), $condition);
	}
	
// gradebook internal item
	function create_internal_item($internal_item)
	{
		return $this->database->create($internal_item);
	}
	
	function retrieve_internal_item_by_publication($application, $publication_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $application);
		$conditions[] = new EqualityCondition(InternalItem :: PROPERTY_PUBLICATION_ID, $publication_id);
		$condition = new AndCondition($conditions);
		return $this->database->retrieve_object(InternalItem :: get_table_name(), $condition);
	}
	
	function retrieve_internal_item($id)
	{
		$condition = new EqualityCondition(InternalItem :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(InternalItem :: get_table_name(), $condition);
	}
	
	function delete_internal_item($internal_item)
	{
		$condition = new EqualityCondition(InternalItem :: PROPERTY_ID, $internal_item->get_id());
		return $this->database->delete(InternalItem :: get_table_name(), $condition);
	}
	
// internal item instance
	
	function delete_internal_item_instance($internal_item_instance)
	{
		$condition = new EqualityCondition(InternalItemInstance :: PROPERTY_ID, $internal_item_instance->get_id());
		return $this->database->delete(InternalItemInstance :: get_table_name(), $condition);
	}
	
	function retrieve_evaluation_ids_by_internal_item_id($internal_item_id)
	{
        $internal_item_instance_alias = $this->database->get_alias(InternalItemInstance :: get_table_name());
        
		$condition = new EqualityCondition(InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID, $internal_item_id);
		$query = 'SELECT ' . $this->database->escape_column_name(InternalItemInstance :: PROPERTY_EVALUATION_ID,$internal_item_instance_alias) . ' FROM ' . $this->database->escape_table_name(InternalItemInstance :: get_table_name()) .  ' AS ' . $internal_item_instance_alias;
		return $this->database->retrieve_record_set($query, InternalItemInstance :: get_table_name(), $condition);
	}
	function retrieve_internal_item_instance_by_evaluation($evaluation_id)
	{
		$condition = new EqualityCondition(InternalItemInstance :: PROPERTY_EVALUATION_ID, $evaluation_id);
		return $this->database->retrieve_object(InternalItemInstance :: get_table_name(), $condition);
	}
	
	function create_internal_item_instance($internal_item_instance)
	{
		return $this->database->create($internal_item_instance);
	}
	function update_internal_item_instance($internal_item_instance)
	{
		$condition = new EqualityCondition(InternalItemInstance :: PROPERTY_ID, $internal_item_instance->get_id());
		return $this->database->update($internal_item_instance, $condition);
	}
	

// gradebook evaluation
	
	function create_evaluation($evaluation)
	{
		return $this->database->create($evaluation);
	}
	
	function retrieve_all_evaluations_on_publication($application, $publication_id, $offset = null, $max_objects = null, $order_by = null)
	{
		$gdm = GradebookDataManager :: get_instance();
	    $udm = UserDataManager :: get_instance();
	                
	    $gradebook_evaluation_alias = $gdm->get_database()->get_alias(Evaluation :: get_table_name());
	    $gradebook_internal_item_alias = $gdm->get_database()->get_alias(InternalItem :: get_table_name());
	    $gradebook_internal_item_instance_alias = $gdm->get_database()->get_alias(InternalItemInstance :: get_table_name()); 
	    $gradebook_grade_evaluation_alias = $gdm->get_database()->get_alias(GradeEvaluation :: get_table_name());  
	    $user_alias = $gdm->get_database()->get_alias(User :: get_table_name());     
	    $user_evaluator_alias = $gdm->get_database()->get_alias(User :: get_table_name()) . '2';   
	    $gradebook_format_alias = $gdm->get_database()->get_alias(Format :: get_table_name());
	                
	    $query = 'SELECT ' . $gradebook_evaluation_alias . '.' . $this->database->escape_column_name(Evaluation :: PROPERTY_ID) . ', ' . $gradebook_evaluation_alias . '.' . $this->database->escape_column_name(Evaluation :: PROPERTY_EVALUATOR_ID) . ', ' . $gradebook_evaluation_alias . '.' . $this->database->escape_column_name(Evaluation :: PROPERTY_EVALUATION_DATE) . ', ' . $gradebook_evaluation_alias . '.' . $this->database->escape_column_name(Evaluation :: PROPERTY_FORMAT_ID); 
	    $query .= ', CONCAT(' . $user_alias . '.' . $this->database->escape_column_name(User :: PROPERTY_LASTNAME) . ', " ",' . $user_alias . '.' . $this->database->escape_column_name(User :: PROPERTY_FIRSTNAME) . ') AS user';
	    $query .= ', CONCAT(' . $user_evaluator_alias . '.' . $this->database->escape_column_name(User :: PROPERTY_LASTNAME) . ', " ",' . $user_evaluator_alias . '.' . $this->database->escape_column_name(User :: PROPERTY_FIRSTNAME) . ') AS evaluator';
	    $query .= ', ' . $gradebook_format_alias . '.' . $this->database->escape_column_name(Format :: PROPERTY_TITLE); 
	    $query .= ', ' . $gradebook_grade_evaluation_alias . '.' . $this->database->escape_column_name(GradeEvaluation :: PROPERTY_SCORE);
	    $query .= ', ' . $gradebook_grade_evaluation_alias . '.' . $this->database->escape_column_name(GradeEvaluation :: PROPERTY_COMMENT);
	    $query .= ' FROM ' . $this->database->escape_table_name(InternalItem :: get_table_name()) . ' AS ' . $gradebook_internal_item_alias;
	    $query .= ' JOIN ' . $this->database->escape_table_name(InternalItemInstance :: get_table_name()) . ' AS ' . $gradebook_internal_item_instance_alias . ' ON ' . $this->database->escape_column_name(InternalItem :: PROPERTY_ID, $gradebook_internal_item_alias) . ' = ' . $this->database->escape_column_name(InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID, $gradebook_internal_item_instance_alias); 
	    $query .= ' JOIN ' . $this->database->escape_table_name(Evaluation :: get_table_name()) . ' AS ' . $gradebook_evaluation_alias . ' ON ' . $this->database->escape_column_name(Evaluation :: PROPERTY_ID, $gradebook_evaluation_alias) . ' = ' . $this->database->escape_column_name(InternalItemInstance :: PROPERTY_EVALUATION_ID, $gradebook_internal_item_instance_alias); 
	    $query .= ' JOIN ' . $this->database->escape_table_name(GradeEvaluation :: get_table_name()) . ' AS ' . $gradebook_grade_evaluation_alias . ' ON ' . $this->database->escape_column_name(GradeEvaluation :: PROPERTY_ID, $gradebook_grade_evaluation_alias) . ' = ' . $this->database->escape_column_name(Evaluation :: PROPERTY_ID, $gradebook_evaluation_alias); 
	    $query .= ' JOIN ' . $udm->escape_table_name(User :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $udm->escape_column_name(User :: PROPERTY_ID, $user_alias) . ' = ' . $this->database->escape_column_name(Evaluation :: PROPERTY_USER_ID, $gradebook_evaluation_alias); 
	    $query .= ' JOIN ' . $udm->escape_table_name(User :: get_table_name()) . ' AS ' . $user_evaluator_alias . ' ON ' . $udm->escape_column_name(User :: PROPERTY_ID, $user_evaluator_alias) . ' = ' . $this->database->escape_column_name(Evaluation :: PROPERTY_EVALUATOR_ID, $gradebook_evaluation_alias);
	    $query .= ' JOIN ' . $this->database->escape_table_name(Format :: get_table_name()) . ' AS ' . $gradebook_format_alias . ' ON ' . $this->database->escape_column_name(Format :: PROPERTY_ID, $gradebook_format_alias) . ' = ' . $this->database->escape_column_name(Evaluation :: PROPERTY_FORMAT_ID, $gradebook_evaluation_alias);
	
		$conditions = array();
	    $conditions[] = new EqualityCondition(InternalItem :: PROPERTY_PUBLICATION_ID, $publication_id, InternalItem :: get_table_name());
	    $conditions[] = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $application, InternalItem :: get_table_name());
	    $condition = new AndCondition($conditions);
        return $this->database->retrieve_object_set($query, Evaluation :: get_table_name(), $condition, $offset, $max_objects, $order_by, Evaluation :: CLASS_NAME);
    }
	
	function retrieve_evaluation_ids_by_publication($application, $publication_id)
	{
		$internal_item = $this->retrieve_internal_item_by_publication($application, $publication_id);
		if(!$internal_item)
			return false;
		return $this->retrieve_evaluation_ids_by_internal_item_id($internal_item->get_id());
	}
	

    function count_all_evaluations_on_publication($publication_id)
    {
        $gdm = GradebookDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        $gradebook_internal_item_instance_alias = $gdm->get_database()->get_alias(InternalItemInstance :: get_table_name());
                
        $query = 'SELECT COUNT(*) FROM ' . $this->database->escape_table_name(InternalItemInstance :: get_table_name()) . ' AS ' . $gradebook_internal_item_instance_alias;
    	$internal_item = $this->retrieve_internal_item_by_publication(Request :: get('application'),$publication_id);
        $condition = new EqualityCondition(InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID, $internal_item->get_id());
       	return $this->database->count_result_set($query, InternalItemInstance :: get_table_name(), $condition);
    }
//	
//	function move_internal_to_external($application, $publication)
//	{
//		$internal_item = $this->retrieve_internal_item_by_publication($application, $publication->get_id());
//		$evaluations_id = $this->retrieve_evaluation_ids_by_internal_item_id($internal_item->get_id())->as_array();
//		$external_item = $this->create_external_item_by_content_object($publication);
//		$ext_item_inst = $this->create_external_item_instance_by_moving($external_item, $evaluations_id);
//		$del_internal_item = $this->delete_internal_item($internal_item);
//		if(!($internal_item || $evaluations_id || $external_item || $ext_item_inst || $del_internal_item))
//			return false;
//		return true;
//	}
	
	function delete_evaluation($evaluation)
	{
		$internal_item_instance = $this->retrieve_internal_item_instance_by_evaluation($evaluation->get_id());
		if (! $this->delete_internal_item_instance($internal_item_instance))
		{
			return false;
		}
		$grade_evaluation = $this->retrieve_grade_evaluation($evaluation->get_id());
		if (! $this->delete_grade_evaluation($grade_evaluation))
		{
			return false;
		}
		$condition = new EqualityCondition(Evaluation :: PROPERTY_ID, $evaluation->get_id());
		return $this->database->delete($evaluation->get_table_name(), $condition);
	}
	
	function update_evaluation($evaluation)
	{
//		dump($evaluation);
//		$grade_evaluation = $this->retrieve_grade_evaluation($evaluation->get_id());
//		dump($grade_evaluation);
//		if (! $this->update_grade_evaluation($grade_evaluation))
//		{
//			return false;
//		}
		$condition = new EqualityCondition(Evaluation :: PROPERTY_ID, $evaluation->get_id());
		return $this->database->update($evaluation, $condition);
	}
	/*
	
	function delete_external_item_instance($external_item_instance)
	{
		$condition = new EqualityCondition(ExternalItemInstance :: PROPERTY_ID, $external_item_instance->get_id());
		return $this->database->delete(ExternalItemInstance :: get_table_name(), $condition);
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
*/
	function retrieve_evaluation($id){
		$condition = new EqualityCondition(Evaluation :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Evaluation :: get_table_name(), $condition);
	}

	function retrieve_evaluations($condition = null, $offset = null, $count = null, $order_property = null){
		return $this->database->retrieve_objects(Evaluation :: get_table_name(), $condition, $offset, $count, $order_property);
	}
	
	function create_evaluation_object_from_data($content_object_publication, $user)
	{
		$evaluation = new Evaluation();
		$evaluation->set_evaluator_id($content_object_publication->get_publisher_user_id());
		$evaluation->set_user_id($user);
		$evaluation->set_evaluation_date((Utilities :: to_db_date($content_object_publication->get_publication_date())));		
		$evaluation->set_format_id($this->retrieve_format_id_by_format_name('percentage'));
		if($this->database->create($evaluation))
			return $evaluation;
		return false;
	}
	
	// evaluation format
	
	function retrieve_format_id_by_format_name($format_name)
	{
		$condition = new EqualityCondition(Format :: PROPERTY_TITLE, $format_name);
		return $this->database->retrieve_object(Format :: get_table_name(), $condition)->get_id();
	}
		
	//gradebook grade evaluation
	function create_grade_evaluation($grade_evaluation)
	{
		return $this->database->create($grade_evaluation, false);
	}
	
	function retrieve_grade_evaluation($id)
	{
		$condition = new EqualityCondition(GradeEvaluation :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(GradeEvaluation :: get_table_name(), $condition);
	}
	
	function delete_grade_evaluation($grade_evaluation)
	{
		$condition = new EqualityCondition(GradeEvaluation :: PROPERTY_ID, $grade_evaluation->get_id());
		return $this->database->delete(GradeEvaluation :: get_table_name(), $condition);		
	}
	
	function update_grade_evaluation($grade_evaluation)
	{
		$condition = new EqualityCondition(GradeEvaluation :: PROPERTY_ID, $grade_evaluation->get_id());
		return $this->database->update($grade_evaluation, $condition);
	}
	
	function create_grade_evaluation_object_from_data($tracker_score)
	{
		$grade_evaluation = new GradeEvaluation();
		$grade_evaluation->set_score($tracker_score);
		$grade_evaluation->set_comment('automatic generated result');
		if($this->database->create($grade_evaluation))
			return $grade_evaluation;
		return false;
	}
	
	//gradebook external item
	
	function create_external_item_by_content_object($content_object_id)
	{
		$rdm = RepositoryDataManager :: get_instance();
        $content_object = $rdm->retrieve_content_object($content_object_id);
		$external_item = new ExternalItem();
		$external_item->set_title($content_object->get_title());
		$external_item->set_description($content_object->get_description());
		if($this->database->create($external_item));
			return $external_item;
		return false;
	}
	
	
	//gradebook external item instance
	
	function create_external_item_instance_by_moving($external_item, $evaluations_id)
	{
		if(is_array($evaluations_id))
		{
			for($i = 0;$i<count($evaluations_id);$i++)
			{
				$id = $evaluations_id[$i]['evaluation_id'];
				if(!$this->create_external_item_instance_function($external_item, $id))
					return false;
			}
		}
		else
		{
			return $this->create_external_item_instance_function($external_item, $evaluations_id);
		}
	}
	
	function create_external_item_instance_function($external_item, $id)
	{
		
		$external_item_instance = new ExternalItemInstance();
		$external_item_instance->set_external_item_id($external_item->get_id());
		$external_item_instance->set_evaluation_id($id);
		if($this->database->create($external_item_instance))
		{
			if($this->retrieve_internal_item_instance_by_evaluation($id))
			{
				if(!$this->delete_internal_item_instance($this->retrieve_internal_item_instance_by_evaluation($id)))
					return false;
			}
		}
		else 
			return false;
		return true;
	}
	
	// applications
	function retrieve_internal_item_applications_with_evaluations()
	{
		$ids = $this->database->retrieve_distinct(InternalItemInstance :: get_table_name(), InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID);
		foreach($ids as $id)
		{
//			$condition = new EqualityCondition(InternalItem :: PROPERTY_ID, $id);
//			$transform_array = $this->database->retrieve_distinct(InternalItem :: get_table_name(), InternalItem :: PROPERTY_APPLICATION, $condition);
			$applications_and_internal_item_id[] = $id;
		}
//		for($i = 0;$i<count($applications);$i++)
//		{
//			$application[$i] = $applications[$i][0];
//		}
//        return array_unique($application);
		return $applications_and_internal_item_id;
	}
	
	function retrieve_calculated_internal_items()
	{
		$condition = new EqualityCondition(InternalItem :: PROPERTY_CALCULATED, 1);
		return $this->database->retrieve_distinct(InternalItem :: get_table_name(), InternalItem :: PROPERTY_ID, $condition);
	}
	
	function retrieve_internal_items_by_application($condition, $offset = null, $count = null, $order_property = null)
	{
		$ids = $this->database->retrieve_distinct(InternalItemInstance :: get_table_name(), InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID);
		$conditions = array();
		$conditions[] = $condition;
		$conditions[] = new InCondition(InternalItem :: PROPERTY_ID, $ids);
		$condition = new AndCondition($conditions);
		return $this->database->retrieve_objects(InternalItem:: get_table_name(), $condition, $offset, $count, $order_property);
	}
	
	function count_internal_items_by_application($condition)
	{
		$ids = $this->database->retrieve_distinct(InternalItemInstance :: get_table_name(), InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID);
		$conditions = array();
		$conditions[] = $condition;
		$conditions[] = new InCondition(InternalItem :: PROPERTY_ID, $ids);
		$condition = new AndCondition($conditions);
		return $this->database->count_objects(InternalItem:: get_table_name(), $condition);
	}
/*
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