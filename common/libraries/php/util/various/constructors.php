<?php

namespace common\libraries;

require_once dirname(__FILE__) . '/../../../../global.inc.php';

$root = Path :: get(SYS_PATH) . 'plugin';

$files = Filesystem :: get_directory_content($root);

foreach ($files as $file)
{
    if (is_dir($file))
    {
        continue;
    }

    if (substr($file, -4) == '.php')
    {
        $contents = file_get_contents($file);
        $regex = '/class [a-zA-Z0-9_-]*/';
        preg_match($regex, $contents, $matches);
	$class = substr($matches[0], 6);
     
        if($class && strpos($contents, 'function ' . $class . '(') !== false)
        {
            $new_contents = str_replace('function ' . $class . '(', 'function __construct(', $contents);
            file_put_contents($file, $new_contents);
            dump('Changed class ' . $class);
        }
    }
}

?>
