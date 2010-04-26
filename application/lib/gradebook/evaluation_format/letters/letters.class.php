<?php
class Letters extends EvaluationFormat
{
	private $score_set = array('A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'F' => 'F');
	
	const DEFAULT_ACTIVE_VALUE = 1;
	const EVALUATION_FORMAT_NAME = 'Letters';
	
	function Letters()
	{
		
	}
	
	//getters and setters
    function get_score_set()
    {
    	return $this->score_set;
    }
    
    function get_evaluation_field_name()
	{
		return 'letters_evaluation';
	}
	
	function get_evaluation_field_type()
	{
		return 'select';
	}
	
	function get_evaluation_format_name()
	{
		return self :: EVALUATION_FORMAT_NAME;
	}
    
	function get_default_active_value()
	{
		return self :: DEFAULT_ACTIVE_VALUE;
	}
	
	function get_formatted_score()
	{
		return $this->get_score();
	}
}
?>