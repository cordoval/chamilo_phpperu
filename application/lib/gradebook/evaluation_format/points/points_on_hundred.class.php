<?php
require_once dirname(__FILE__) . '/../evaluation_format.class.php';

class PointsOnHundred extends EvaluationFormat
{
	const MIN_VALUE = 0;
	const MAX_VALUE = 100;
	const STEP = 1;
	
	const DEFAULT_ACTIVE_VALUE = 0;
	const EVALUATION_FORMAT_NAME = 'points on hundred';
	
	function PointsOnHundred()
	{
		
	}
	
	//getters and setters
    function get_score_set()
    {
    	return null;
    }
    
	function get_evaluation_field_type()
	{
		return 'text';
	}
	
	function get_evaluation_field_name()
	{
		return 'points_evaluation';
	}
	
	function get_score_information()
	{
		return 'Minimum value: ' . self :: MIN_VALUE . ' - Maximum value: ' . self :: MAX_VALUE . ' - Step: ' . self :: STEP;
	}
	
	function get_evaluation_format_name()
	{
		return self :: EVALUATION_FORMAT_NAME;
	}
	
	function get_min_value()
	{
		return self :: MIN_VALUE;
	}
	
	function get_max_value()
	{
		return self :: MAX_VALUE;
	}
	
	function get_step()
	{
		return self :: STEP;
	}
	
	function get_default_active_value()
	{
		return self :: DEFAULT_ACTIVE_VALUE;
	}
}
?>