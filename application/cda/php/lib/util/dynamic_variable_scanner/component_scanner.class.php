<?php

namespace application\cda;

use common\libraries\Filesystem;

require_once dirname(__FILE__) . '/../files_scanner.class.php';

class ComponentScanner extends FilesScanner
{
    function scan_file($file, $contents, $namespace)
    {
        $regex = '/class [a-zA-Z0-9]*/';
        $parts = explode('/', $file);
	if($parts[count($parts) - 2] == 'component')
	{
		if(strpos($contents, 'DelegateComponent') != false)
		{
			return;
		}

		preg_match($regex, $contents, $matches);
		$class = substr($matches[0], 6);

		$dir = dirname(__FILE__) . '/../translations/' . str_replace('\\', '//', $namespace) . '/resources/i18n/';
                if (!file_exists($dir))
                {
                    Filesystem :: create_dir($dir);
                }

                $file = $dir . '/english.i18n';
                $translations_handle = fopen($file, 'a+');
                fwrite($translations_handle, $class . ' = ' . "\n");
                fclose($file);

	}
    }

}

?>