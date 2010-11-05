<?php

namespace application\cda;

use common\libraries\Path;
use common\libraries\WebApplication;
use common\libraries\Filesystem;

/**
 * Scans files from one or more applications
 * @author Sven Vanpoucke
 */

abstract class FilesScanner
{
    function scan_application($application)
    {
        if($application == 'common')
        {
            $directory = Path :: get(SYS_PATH) . 'common/';
        }
        else
        {
            $directory = Path :: get(SYS_PATH) . (WebApplication :: is_application($application) ? '/application/'  : '') . $application;
        }
        $this->scan_files($directory);
    }

    function scan_files($root)
    {
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
                $pattern = '/namespace (\w+[\\\\a-z_]*)/';
                $matches = array();
                preg_match($pattern, $contents, $matches);
                $namespace = $matches[1];

                $this->scan_file($file, $contents, $namespace);

            }
        }
    }

    abstract function scan_file($file, $contents, $namespace);
}

?>
