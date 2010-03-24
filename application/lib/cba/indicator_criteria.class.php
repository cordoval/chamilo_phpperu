<?php
/**
 * This class describes the link between an indicator and a criteria
 * 
 * @author Nick Van Loocke
 */
class IndicatorCriteria extends DataClass
{
	const CLASS_NAME = __CLASS__;
	
	/**
	 * Indicator/Criteria properties
	 */	
    const PROPERTY_INDICATOR_ID = 'indicator_id'; 
    const PROPERTY_CRITERIA_ID = 'criteria_id';   


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_CRITERIA_ID, self :: PROPERTY_INDICATOR_ID);
	}

	function get_data_manager()
	{
		return CbaDataManager :: get_instance();
	}
	
	function get_indicator_id()
	{
		return $this->get_default_property(self :: PROPERTY_INDICATOR_ID);
	}
	
	function set_indicator_id($indicator_id)
	{
		$this->set_default_property(self :: PROPERTY_INDICATOR_ID, $indicator_id);
	}
	
	function get_criteria_id()
	{
		return $this->get_default_property(self :: PROPERTY_CRITERIA_ID);
	}
	
	function set_criteria_id($criteria_id)
	{
		$this->set_default_property(self :: PROPERTY_CRITERIA_ID, $criteria_id);
	}
	
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>