<?php
/**
 *	This is a skeleton for a data manager for the Cba Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Nick Van Loocke
 */
abstract class CbaDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	protected function CbaDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return CbaDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.Utilities :: camelcase_to_underscores($type).'.class.php';
			$class = $type.'CbaDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	abstract function initialize();
	abstract function create_storage_unit($name,$properties,$indexes);

	// Abstract functions: Competency
	abstract function get_next_competency_id();
	abstract function create_competency($competency);
	abstract function update_competency($competency);
	abstract function delete_competency($competency);
	abstract function count_competencys($conditions = null);
	abstract function retrieve_competency($id);
	abstract function retrieve_competencys($condition = null, $offset = null, $count = null, $order_property = null);
	
	abstract function retrieve_competency_categories($condition = null, $offset = null, $count = null, $order_property = null);
	abstract function count_competency_categories($conditions = null);
		
	// Abstract functions: Indicator
	abstract function get_next_indicator_id();
	abstract function create_indicator($indicator);
	abstract function update_indicator($indicator);
	abstract function delete_indicator($indicator);
	abstract function count_indicators($conditions = null);
	abstract function retrieve_indicator($id);
	abstract function retrieve_indicators($condition = null, $offset = null, $count = null, $order_property = null);
	
	abstract function retrieve_indicator_categories($condition = null, $offset = null, $count = null, $order_property = null);
	abstract function count_indicator_categories($conditions = null);
	
	// Abstract functions: Criteria
	abstract function get_next_criteria_id();
	abstract function create_criteria($criteria);
	abstract function update_criteria($criteria);
	abstract function delete_criteria($criteria);
	abstract function count_criterias($conditions = null);
	abstract function retrieve_criteria($id);
	abstract function retrieve_criterias($condition = null, $offset = null, $count = null, $order_property = null);
	
	abstract function retrieve_criteria_categories($condition = null, $offset = null, $count = null, $order_property = null);
	abstract function count_criteria_categories($conditions = null);
	
	// Abstract functions: CriteriaScore
	abstract function get_next_criteria_score_id();
	abstract function create_criteria_score($criteria_score);
	abstract function update_criteria_score($criteria_score);
	abstract function delete_criteria_score($criteria_score);
	abstract function count_criterias_score($conditions = null);
	abstract function retrieve_criteria_score($id);
	abstract function retrieve_criteria_score_unique($id, $criteria_id);
	abstract function retrieve_criterias_score($condition = null, $offset = null, $count = null, $order_property = null);
	
	
	// Abstract functions: CompetencyIndicator
	abstract function get_next_competency_indicator_id();
	abstract function create_competency_indicator($competency_indicator);
	abstract function update_competency_indicator($competency_indicator);
	abstract function delete_competency_indicator($competency_indicator);
	abstract function count_competencys_indicator($conditions = null);
	abstract function retrieve_competency_indicator($id);
	abstract function retrieve_competencys_indicator($condition = null, $offset = null, $count = null, $order_property = null);
	
	
	// Abstract functions: IndicatorCriteria
	abstract function get_next_indicator_criteria_id();
	abstract function create_indicator_criteria($indicator_criteria);
	abstract function update_indicator_criteria($indicator_criteria);
	abstract function delete_indicator_criteria($indicator_criteria);
	abstract function count_indicators_criteria($conditions = null);
	abstract function retrieve_indicator_criteria($id);
	abstract function retrieve_indicators_criteria($condition = null, $offset = null, $count = null, $order_property = null);
	
}
?>