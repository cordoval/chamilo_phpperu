<?php 
/**
 * This class describes a CriteriaScore data object
 * 
 * @author Nick Van Loocke
 */
class CriteriaScore extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
     * Constant to define the normal state of a learning object
     */
    const STATE_NORMAL = 0;
    /**
     * Constant to define the recycled state of a learning object (= learning
     * object is moved to recycle bin)
     * Recycle bin is not used in the CBA application
     */
    const STATE_RECYCLED = 1;
    /**
     * Constant to define the backup state of a learning object
     */
    const STATE_BACKUP = 2;
	
	/**
	 * Criteria properties
	 */
    const PROPERTY_CRITERIA_ID = 'criteria_id';
	const PROPERTY_DESCRIPTION_SCORE = 'description_score';
	const PROPERTY_SCORE = 'score';
	const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_STATE = 'state';


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_CRITERIA_ID, self :: PROPERTY_DESCRIPTION_SCORE, self :: PROPERTY_SCORE, self :: PROPERTY_OWNER_ID, self :: PROPERTY_STATE);
	}

	function get_data_manager()
	{
		return CbaDataManager :: get_instance();
	}

	function get_criteria_id()
	{
		return $this->get_default_property(self :: PROPERTY_CRITERIA_ID);
	}

	function set_criteria_id($criteria_id)
	{
		$this->set_default_property(self :: PROPERTY_CRITERIA_ID, $criteria_id);
	}

	function get_description_score()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION_SCORE);
	}

	function set_description_score($description_score)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION_SCORE, $description_score);
	}
	
	function get_score()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE);
	}

	function set_score($score)
	{
		$this->set_default_property(self :: PROPERTY_SCORE, $score);
	}
	
	function get_owner_id()
	{
		return $this->get_default_property(self :: PROPERTY_OWNER_ID);
	}

	function set_owner_id($owner_id)
	{
		$this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
	}

	function get_state()
	{
		return $this->get_default_property(self :: PROPERTY_STATE);
	}

	function set_state($state)
	{
		$this->set_default_property(self :: PROPERTY_STATE, $state);
	}
	
	function get_target_scores()
	{
		if (! $this->target_scores)
        {
            $condition = new EqualityCondition(CriteriaScore :: PROPERTY_CRITERIA_ID, $this->get_criteria_id());
            $scores = CbaDataManager :: get_instance()->retrieve_criterias_score($condition, null, null, new ObjectTableOrder(IndicatorCriteria :: PROPERTY_CRITERIA_ID));

            while ($score = $scores->next_result())
            {
                $this->target_scores[] = $score->get_id();
            }          
        }
        return $this->target_scores;
	}
	
	function set_target_scores($target_scores)
	{
        $this->target_scores = $target_scores;
	}

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>