<?php
class LettersEvaluationFormat extends EvaluationFormat
{
	private $score;
	private $score_set = array('A', 'B', 'C', 'D', 'F');
	const DEFAULT_ACTIVE_VALUE = 1;
	
	function LettersEvaluationFormat()
	{
		
	}
	
	//getters and setters
 	function set_score($score)
    {
    	if(array_key_exists($score, $content))
    		$this->score = $score;
    }
		
    function get_score()
    {
    	return $this->score;
    }
    
    function get_score_set()
    {
    	return $this->score_set;
    }
    
    function get_evaluation()
    {
    	$this->get_score();
    }
    
	function get_evaluation_field_type()
	{
		return 'select';
	}
    
	function get_default_active_value()
	{
		return self :: DEFAULT_ACTIVE_VALUE;
	}
}
?>