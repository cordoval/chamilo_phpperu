<?php

namespace application\cda;

use common\libraries\Path;
use common\libraries\WebApplication;
use common\libraries\Filesystem;


abstract class VariableScanner
{
    function scan_application($application)
    {
        $this->write_to_files = $write_to_files;

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

    private function scan_files($root)
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

                $this->scan_for_valid_variables($contents, $namespace);

            }
        }
    }

    private function scan_for_valid_variables($contents, $namespace)
    {
        $pattern = '/Translation :: get\([\'"](\w+)[\'"][,\s]*(array\(.*?\))*.*?([\'"](.*?)[\'"])?\)/i';
        $matches = array();
        preg_match_all($pattern, $contents, $matches);

        foreach ($matches[1] as $index => $match)
        {
            $context = $matches[4][$index];
            if(!$context || trim($context) != '')
            {
                $context = $namespace;
            }

            $this->handle_variable($match, $context);
        }
    }
    
    abstract function handle_variable($variable, $context);

}

?>