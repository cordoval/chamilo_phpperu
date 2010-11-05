<?php

namespace application\cda;

require_once dirname(__FILE__) . '/../files_scanner.class.php';

abstract class VariableScanner extends FilesScanner
{
    function scan_file($file, $contents, $namespace)
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