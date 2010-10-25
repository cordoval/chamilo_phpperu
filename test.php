<?php

require_once dirname(__FILE__) . '/common/global.inc.php';

$applications = array('webservice');

foreach($applications as $application)
{
	$file = dirname(__FILE__) . '/' . $application . '/test/' . $application . '_test_suite.class.php';
	if(!file_exists($file))
	{
		Display :: header();
		Display :: error_message(Translation :: get('CanNotLoadTestSuite', array('FILE' => $file)));	
		Display :: footer();
		exit;
	}
	
	require_once($file);
	$class = Utilities :: underscores_to_camelcase($application) . 'TestSuite';
	//$test = new $class();
}

?>