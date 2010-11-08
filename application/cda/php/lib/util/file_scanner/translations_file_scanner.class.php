<?php

namespace application\cda;

require_once dirname(__FILE__) . '/file_scanner.class.php';

class TranslationsFileScanner extends FileScanner
{
    function scan_file($file, $contents, $namespace, $variable_writer)
    {
        $pattern = '/Translation :: get\([\'"](\w+)[\'"][,\s]*(array\(.*?\))*.*?([\'"](.*?)[\'"])?\)/i';
        $matches = array();
        preg_match_all($pattern, $contents, $matches);
dump($matches);
        foreach ($matches[1] as $index => $match)
        {
            $context = $matches[4][$index];
            if(!$context || trim($context) != '')
            {
                $context = $namespace;
            }

            $variable_writer->handle_variable($match, $context);
        }
    }
}

?>