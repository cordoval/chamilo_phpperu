<?php

namespace application\package;

use common\libraries\Filesystem;

require_once dirname(__FILE__) . '/file_scanner.class.php';

class ComponentFileScanner extends FileScanner
{
    function scan_file($file, $contents, $namespace, $variable_writer)
    {
        $regex = '/class [a-zA-Z0-9]*/';
        $file = str_replace('\\', '/', $file);
        $parts = explode('/', $file);
	if($parts[count($parts) - 2] == 'component')
	{
		if(strpos($contents, 'DelegateComponent') != false)
		{
			return;
		}

		preg_match($regex, $contents, $matches);
		$class = substr($matches[0], 6);

		$variable_writer->handle_variable($class, $namespace);

	}
    }

}

?>