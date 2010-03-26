<?php
abstract class EvaluationFormat
{
		
	abstract function add_to_form($form);

	
	function factory()
	{
		require_once dirname(__FILE__) . '/' . strtolower($type).  '/'. strtolower($type) . 'evaluation_format.class.php';
        $class = $type . 'ReportingFormat';
        return new $class($reporting_block);
	}
	

}
?>