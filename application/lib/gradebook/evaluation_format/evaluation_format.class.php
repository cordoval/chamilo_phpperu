<?php
abstract class EvaluationFormat
{
    //returns how the evaluation should be shown on screen
	abstract function get_evaluation();

	function factory($type)
	{
		require_once dirname(__FILE__) . '/' . strtolower($type).  '/'. strtolower($type) . '_evaluation_format.class.php';
        $class = ucfirst($type) . 'EvaluationFormat';
        return new $class();
	}
	
	abstract function get_evaluation_field_type();
	
	abstract function get_default_active_value();
	
	abstract function get_score_set();
}
?>