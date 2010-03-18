<?php 
/**
 * This class describes a Criteria data object
 * 
 * @author Nick Van Loocke
 */
class Criteria extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
     * Constant to define the normal state of a learning object
     */
    const STATE_NORMAL = 0;
    /**
     * Constant to define the recycled state of a learning object (= learning
     * object is moved to recycle bin)
     */
    const STATE_RECYCLED = 1;
    /**
     * Constant to define the backup state of a learning object
     */
    const STATE_BACKUP = 2;
	
	/**
	 * Criteria properties
	 */
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_STATE = 'state';
    
    const PROPERTY_DESCRIPTION_SCORE = 'description_score';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK_ID = 'feedback_id';


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_OWNER_ID, self :: PROPERTY_PARENT_ID, self :: PROPERTY_STATE);
	}

	function get_data_manager()
	{
		return CbaDataManager :: get_instance();
	}

	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}

	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
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
	
	function get_feedback_id()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
	}

	function set_feedback_id($feedback_id)
	{
		$this->set_default_property(self :: PROPERTY_FEEDBACK_ID, $feedback_id);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function move($new_parent_id)
    {
    	$this->set_parent_id($new_parent_id);
    	$cdm = CbaDataManager :: get_instance();
        return $cdm->update_criteria($this);
    }
}

?>