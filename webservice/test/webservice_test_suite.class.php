<?php 

require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once Path :: get_plugin_path() . 'simpletest/unit_tester.php';
require_once Path :: get_plugin_path() . 'simpletest/autorun.php';

class WebserviceTestSuite extends TestSuite
{
	function __construct()
	{
		parent :: __construct('All test cases');
		$dir = dirname(__FILE__) . '/test_cases/';
		$files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES, false);
		foreach($files as $file)
		{
			if(substr($file, 0, 1) != '.')
			{
				$this->addFile($dir . $file);
			}
		}
	}
}

$test = new WebserviceTestSuite();

?>