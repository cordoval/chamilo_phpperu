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
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_STATE = 'state';


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_CRITERIA_ID, self :: PROPERTY_DESCRIPTION_SCORE, self :: PROPERTY_SCORE, self :: PROPERTY_OWNER_ID, self :: PROPERTY_PARENT_ID, self :: PROPERTY_STATE);
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

	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}

	function set_parent_id($parent_id)
	{
		$this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
	}

	function get_state()
	{
		return $this->get_default_property(self :: PROPERTY_STATE);
	}

	function set_state($state)
	{
		$this->set_default_property(self :: PROPERTY_STATE, $state);
	}

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	/*function move($new_parent_id)
    {
    	$this->set_parent_id($new_parent_id);
    	$cdm = CbaDataManager :: get_instance();
        return $cdm->update_criteria($this);
    }*/
}

?>