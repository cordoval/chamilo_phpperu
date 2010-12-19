<?php
namespace common\libraries;

require_once dirname(__FILE__) . '/../../../../global.inc.php';

$root = Path :: get(SYS_PATH);

$files = Filesystem :: get_directory_content($root);

foreach ($files as $file)
{
    if (is_dir($file))
    {
        continue;
    }

    if (substr($file, - 4) == '.php')
    {
        $contents = file_get_contents($file);
        $regex = '/class [a-zA-Z0-9_-]*/';
        preg_match_all($regex, $contents, $matches);

        foreach ($matches[0] as $match)
        {
            $class = substr($match, 6);

            if ($class && strpos($contents, 'function ' . $class . '(') !== false)
            {
                $contents = str_replace('function ' . $class . '(', 'function __construct(', $contents);
                dump('Changed class ' . $class);
            }
        }

        file_put_contents($file, $contents);
    }
}
?>