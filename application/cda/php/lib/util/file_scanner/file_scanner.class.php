<?php
namespace application\cda;

use common\libraries\Utilities;

abstract class FileScanner
{
    abstract function scan_file($file, $contents, $namespace, $variable_writer);

    function factory($type)
    {
        $file = dirname(__FILE__) . '/' . $type . '_file_scanner.class.php';
        if($file)
        {
            require_once $file;
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'FileScanner';
            return new $class;
        }
    }
}

?>
