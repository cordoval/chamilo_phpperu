<?php
/**
 * This class describes the link between a competency and an indicator
 * 
 * @author Nick Van Loocke
 */
class CompetencyIndicator extends DataClass
{
	const CLASS_NAME = __CLASS__;
	
	/**
	 * Competency/Indicator properties
	 */
	const PROPERTY_COMPETENCY_ID = 'competency_id';
    const PROPERTY_INDICATOR_ID = 'indicator_id';    


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_COMPETENCY_ID, self :: PROPERTY_INDICATOR_ID);
	}

	function get_data_manager()
	{
		return CbaDataManager :: get_instance();
	}

	function get_competency_id()
	{
		return $this->get_default_property(self :: PROPERTY_COMPETENCY_ID);
	}
	
	function set_competency_id($competency_id)
	{
		$this->set_default_property(self :: PROPERTY_COMPETENCY_ID, $competency_id);
	}
	
	function get_indicator_id()
	{
		return $this->get_default_property(self :: PROPERTY_INDICATOR_ID);
	}
	
	function set_indicator_id($indicator_id)
	{
		$this->set_default_property(self :: PROPERTY_INDICATOR_ID, $indicator_id);
	}
	
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>