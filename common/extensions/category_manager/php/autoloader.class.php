<?php
namespace common\extensions\category_manager;

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
            $list = array('category_manager' => 'category_manager.class.php',
                          'platform_category' => 'platform_category.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);

            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'category_manager/php/' . $url;
                return true;
            }

            return false;
	}
}

?>