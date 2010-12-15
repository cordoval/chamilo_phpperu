<?php

namespace application\package;

use common\libraries\Filesystem;

require_once dirname(__FILE__) . '/variable_writer.class.php';

class FileVariableWriter extends VariableWriter
{
    private $used_variables;

    function handle_variable($variable_name, $context)
    {
        if($this->used_variables[$context][$variable_name])
            return;

        $dir = dirname(__FILE__) . '/../translations/' . str_replace('\\', '//', $context) . '/resources/i18n/';
        if (!file_exists($dir))
        {
            Filesystem :: create_dir($dir);
        }

        $file = $dir . '/english.i18n';
        $translations_handle = fopen($file, 'a+');
        fwrite($translations_handle, $variable_name . ' = ""' . "\n");
        fclose($file);

        $this->used_variables[$context][$variable_name] = true;
    }

}

?>