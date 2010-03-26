<?php
require_once dirname(__FILE__) . '/../evaluation_format.class.php';

class PointsEvaluationFormat extends EvaluationFormat
{
	private $base_score;
	private $score;
	const DEFAULT_ACTIVE_VALUE = 1;
	
	//getters and setters
 	function set_base_score($base_score)
    {
    	if (is_numeric($base_score))
    		$this->base_score = $base_score;
    }  
		
    function get_base_score()
    {
    	return $this->base_score;
    }
	
    function set_score($score)
    {
    	if (is_numeric($score))
    		$this->score = $score;
    }  
		
    function get_score()
    {
    	return $this->score;
    }
    
    function get_evaluation()
    {
    	return $this->get_score() . ' / ' . $this->get_base_score();
    }
    
	function add_to_form($form)
	{
		
	}
	
	function get_default_active_value()
	{
		return self :: DEFAULT_ACTIVE_VALUE;
	}
}
?>