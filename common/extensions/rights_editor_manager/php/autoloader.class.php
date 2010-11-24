<?php

namespace common\extensions\rights_editor_manager;

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
        $list = array('rights_editor_manager' => 'rights_editor_manager.class.php', 'rights_editor_manager_browser_component' => 'component/browser.class.php',
            'rights_editor_manager_template_rights_setter_component' => 'component/template_rights_setter.class.php',
            'rights_editor_manager_user_rights_setter_component' => 'component/user_rights_setter.class.php',
            'rights_editor_manager_group_rights_setter_component' => 'component/group_rights_setter.class.php'
        );
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once Path :: get_common_extensions_path() . 'rights_editor_manager/php/' . $url;
            return true;
        }

        return false;
    }

}

?>