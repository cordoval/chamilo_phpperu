<?php
abstract class EvaluationFormat
{
    //returns how the evaluation should be shown on screen
	abstract function get_evaluation();

	function factory($type)
	{
		require_once dirname(__FILE__) . '/' . strtolower($type).  '/'. strtolower($type) . '_evaluation_format.class.php';
        $class = $type . 'EvaluationFormat';
        return new $class($reporting_block);
	}
}
?>