<?php
class LettersEvaluationFormat extends EvaluationFormat
{
	private $score;
	private $content = array('A', 'B', 'C', 'D', 'E', 'F');
	const DEFAULT_ACTIVE_VALUE = 1;
	
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
    
    function get_evaluation()
    {
    	$this->get_score();
    }
	
	function add_to_form($form)
	{
		
	}
}
?>