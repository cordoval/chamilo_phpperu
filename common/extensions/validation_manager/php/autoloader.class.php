<?php

namespace common\extensions\validation_manager;

use common\libraries\Utilities;
use common\libraries\Path;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array(
            'validation_manager' => 'validation_manager.class.php',
            'default_validation_table_column_model' => 'validation_table/default_validation_table_column_model.class.php',
            'default_validation_table_cell_renderer' => 'validation_table/default_validation_table_cell_renderer.class.php',
            );
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once Path :: get_common_extensions_path() . 'validation_manager/php/' . $url;
            return true;
        }

        return false;
    }

}

?>