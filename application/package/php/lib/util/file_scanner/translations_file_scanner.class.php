<?php

namespace application\package;

require_once dirname(__FILE__) . '/file_scanner.class.php';

class TranslationsFileScanner extends FileScanner
{
    private $pattern = '/Translation[\s]*::[\s]*get\([\'"]([A-Za-z]*)[\'"](?:,[^,\(\)]*(\((?:[^\(\)]*|(?2))*\))?[^,\)\(]*(?:[,\s]+[\'"]*(.*?)[\'"]*)?)?\)/';
    private $variable_writer;
    private $namespace;

    function scan_file($file, $contents, $namespace, $variable_writer)
    {
        $this->variable_writer = $variable_writer;
        $this->namespace = $namespace;

        //$pattern = '/Translation :: get\([\'"](\w+)[\'"][,\s]*(array\(.*\)|.)*?([,\s]+[\'"]*(.*?)[\'"]*)?\)/i';

        $matches = array();
        preg_match_all($this->pattern, $contents, $matches);
        $this->check_matches($matches);
    }

    function check_matches($matches)
    {
        foreach ($matches[1] as $index => $match)
        {
            $recursive_matches = array();
            preg_match_all($this->pattern, $matches[2][$index], $recursive_matches);
            if(count($recursive_matches) > 0)
            {
                $this->check_matches($recursive_matches);
            }

            $context = $matches[3][$index];
            if(!$context || trim($context) == '')
            {
                $context = $this->namespace;
            }

            $this->variable_writer->handle_variable($match, $context);
        }
    }
}

?>