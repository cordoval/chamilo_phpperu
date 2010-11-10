<?php

namespace application\cda;

use common\libraries\Path;
use common\libraries\WebApplication;
use common\libraries\Filesystem;

/**
 * Scans files from one or more applications
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/variable_writer/variable_writer.class.php';
require_once dirname(__FILE__) . '/file_scanner/file_scanner.class.php';

class VariableScanner
{
    private $scanners;
    private $variable_writer;

    function VariableScanner($scanners = array('translations'), $variable_writer_type = 'file')
    {
        foreach($scanners as $scanner)
        {
            $this->scanners[] = FileScanner :: factory($scanner);
        }
        $this->variable_writer = VariableWriter :: factory($variable_writer_type);
    }

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

    function scan_file($file, $contents, $namespace)
    {
        foreach($this->scanners as $scanner)
        {
            $scanner->scan_file($file, $contents, $namespace, $this->variable_writer);
        }
    }
}

?>
