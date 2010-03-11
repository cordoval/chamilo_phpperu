<?php
require_once dirname(__FILE__).'/../competency.class.php';
require_once dirname(__FILE__).'/../indicator.class.php';
require_once dirname(__FILE__).'/../criteria.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author Nick Van Loocke
 */

class DatabaseCbaDataManager extends CbaDataManager
{
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases[Competency :: get_table_name()] = 'competency';
		$aliases[Indicator :: get_table_name()] = 'indicator';
		$aliases[Criteria :: get_table_name()] = 'criteria';

		$this->database = new Database($aliases);
		$this->database->set_prefix('cba_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}
	
	// Competency CRUD, ...
	
	function get_next_competency_id()
	{
		return $this->database->get_next_id(Competency :: get_table_name());
	}

	function create_competency($competency)
	{
		return $this->database->create($competency);
	}
	
	function update_competency($competency)
	{
		$condition = new EqualityCondition(Competency :: PROPERTY_ID, $competency->get_id());
		return $this->database->update($competency, $condition);
	}
	
	function delete_competency($competency)
	{
		$condition = new EqualityCondition(Competency :: PROPERTY_ID, $competency->get_id());
		return $this->database->delete($competency->get_table_name(), $condition);
	}

	function count_competencys($condition = null)
	{
		return $this->database->count_objects(Competency :: get_table_name(), $condition);
	}
	
	function retrieve_competency($id)
	{
		$condition = new EqualityCondition(Competency :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Competency :: get_table_name(), $condition);
	}
	
	function retrieve_competencys($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Competency :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}
	
	
	// Indicator CRUD, ...
	
	function get_next_indicator_id()
	{
		return $this->database->get_next_id(Indicator :: get_table_name());
	}

	function create_indicator($indicator)
	{
		return $this->database->create($indicator);
	}
	
	function update_indicator($indicator)
	{
		$condition = new EqualityCondition(Indicator :: PROPERTY_ID, $indicator->get_id());
		return $this->database->update($indicator, $condition);
	}
	
	function delete_indicator($indicator)
	{
		$condition = new EqualityCondition(Indicator :: PROPERTY_ID, $indicator->get_id());
		return $this->database->delete($indicator->get_table_name(), $condition);
	}

	function count_indicators($condition = null)
	{
		return $this->database->count_objects(Indicator :: get_table_name(), $condition);
	}
	
	function retrieve_indicator($id)
	{
		$condition = new EqualityCondition(Indicator :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Indicator :: get_table_name(), $condition);
	}
	
	function retrieve_indicators($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Indicator :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}
	
	
	// Criteria CRUD, ...
	
	function get_next_criteria_id()
	{
		return $this->database->get_next_id(Criteria :: get_table_name());
	}

	function create_criteria($criteria)
	{
		return $this->database->create($criteria);
	}
	
	function update_criteria($criteria)
	{
		$condition = new EqualityCondition(Criteria :: PROPERTY_ID, $criteria->get_id());
		return $this->database->update($criteria, $condition);
	}
	
	function delete_criteria($criteria)
	{
		$condition = new EqualityCondition(Criteria :: PROPERTY_ID, $criteria->get_id());
		return $this->database->delete($criteria->get_table_name(), $condition);
	}

	function count_criterias($condition = null)
	{
		return $this->database->count_objects(Criteria :: get_table_name(), $condition);
	}
	
	function retrieve_criteria($id)
	{
		$condition = new EqualityCondition(Criteria :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Criteria :: get_table_name(), $condition);
	}
	
	function retrieve_criterias($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Criteria :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}
	
	
	
	// Categories
	
	function retrieve_competency_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(CompetencyCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }
    
	function count_competency_categories($condition = null)
    {
        return $this->database->count_objects(CompetencyCategory :: get_table_name(), $condition);
    }
         
	function create_competency_category($competency_category)
    {
        return $this->database->create($competency_category);
    }

	function update_competency_category($competency_category)
    {
        $condition = new EqualityCondition(CompetencyCategory :: PROPERTY_ID, $competency_category->get_id());
        return $this->database->update($competency_category, $condition);
    }

    function delete_competency_category($competency_category)
    {
        $condition = new EqualityCondition(CompetencyCategory :: PROPERTY_ID, $competency_category->get_id());
        return $this->database->delete($competency_category->get_table_name(), $condition);
    }
    
	function retrieve_indicator_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(IndicatorCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }
    
	function count_indicator_categories($condition = null)
    {
        return $this->database->count_objects(IndicatorCategory :: get_table_name(), $condition);
    }
    
	function create_indicator_category($indicator_category)
    {
        return $this->database->create($indicator_category);
    }
    
	function update_indicator_category($indicator_category)
    {
        $condition = new EqualityCondition(IndicatorCategory :: PROPERTY_ID, $indicator_category->get_id());
        return $this->database->update($indicator_category, $condition);
    }

    function delete_indicator_category($indicator_category)
    {
        $condition = new EqualityCondition(IndicatorCategory :: PROPERTY_ID, $indicator_category->get_id());
        return $this->database->delete($indicator_category->get_table_name(), $condition);
    }
    
	function retrieve_criteria_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(CriteriaCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }
    
	function count_criteria_categories($condition = null)
    {
        return $this->database->count_objects(CriteriaCategory :: get_table_name(), $condition);
    }
    
	function create_criteria_category($criteria_category)
    {
        return $this->database->create($criteria_category);
    }
    
	function update_criteria_category($criteria_category)
    {
        $condition = new EqualityCondition(CriteriaCategory :: PROPERTY_ID, $criteria_category->get_id());
        return $this->database->update($criteria_category, $condition);
    }

    function delete_criteria_category($criteria_category)
    {
        $condition = new EqualityCondition(CriteriaCategory :: PROPERTY_ID, $criteria_category->get_id());
        return $this->database->delete($criteria_category->get_table_name(), $condition);
    }

    
	function retrieve_max_sort_value($table_name, $column, $condition)
    {
        return $this->database->retrieve_max_sort_value($table_name, $column, $condition);
    }
}
?>