<?php
namespace common\libraries;


require_once Path :: get_plugin_path() . 'simpletest/test_case.php';
use \TestSuite;


class ChamiloTestSuite extends TestSuite
{
	function __construct($title, $directory)
	{
		parent :: __construct($title);
		$files = Filesystem :: get_directory_content($directory, Filesystem :: LIST_FILES, false);
		foreach($files as $file)
		{ 
			if(substr($file, 0, 1) != '.')
			{
				$this->addFile($directory . $file);
			}
		}
	}
}

?>