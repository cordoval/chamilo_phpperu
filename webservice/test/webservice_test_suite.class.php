<?php 

require_once dirname(__FILE__) . '/../../common/global.inc.php';

class WebserviceTestSuite extends ChamiloTestSuite
{
	function __construct()
	{
		parent :: __construct('Webservice test cases', dirname(__FILE__) . '/test_cases/');
	}
}

$test = new WebserviceTestSuite();
?>