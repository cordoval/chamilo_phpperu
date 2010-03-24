<?php 
/**
 * This class describes a Indicator data object
 * 
 * @author Nick Van Loocke
 */
class Indicator extends DataClass
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
	 * Indicator properties
	 */
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_STATE = 'state';
    
    const PROPERTY_TARGET_CRITERIAS = 'target_criterias';


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_OWNER_ID, self :: PROPERTY_PARENT_ID, self :: PROPERTY_STATE, self :: PROPERTY_TARGET_CRITERIAS);
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


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	
	function set_target_criterias($target_criterias)
    {
        $this->target_criterias = $target_criterias;
    }
    
	function get_target_criterias()
    {
        if (! $this->target_criterias)
        {
            $condition = new EqualityCondition(Criteria :: PROPERTY_ID, $this->get_id());
            $criterias = $this->get_data_manager()->retrieve_criterias($condition);
            
            while ($criteria = $criterias->next_result())
            {
                $this->target_criterias[] = $user->get_criteria();
            }
        }
        
        return $this->target_users;
    }
	
	
	function move($new_parent_id)
    {
    	$this->set_parent_id($new_parent_id);
    	$cdm = CbaDataManager :: get_instance();
        return $cdm->update_indicator($this);
    }
}

?>