<?php

require_once Path :: get_plugin_path() . 'simpletest/unit_tester.php';
require_once Path :: get_plugin_path() . 'simpletest/autorun.php';

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