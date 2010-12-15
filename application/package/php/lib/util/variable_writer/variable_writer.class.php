<?php
namespace application\package;

use common\libraries\Utilities;

abstract class VariableWriter
{
    abstract function handle_variable($variable_name, $context);

    static function factory($type)
    {
        $file = dirname(__FILE__) . '/' . $type . '_variable_writer.class.php';
        if($file)
        {
            require_once $file;
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'VariableWriter';
            return new $class;
        }
    }
}

?>
