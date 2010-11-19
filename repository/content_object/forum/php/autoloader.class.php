<?php
namespace repository\content_object\forum;

use common\libraries\Utilities;

/**
 * $Id: user_autoloader 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array('forum' => 'forum',
                'forum_builder' => 'builder/forum_builder',
                'forum_complex_display_support' => 'display/forum_complex_display_support',
                'forum_complex_display_preview' => 'display/forum_complex_display_preview',
                'forum_display' => 'display/forum_display');
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url . '.class.php';
            return true;
        }

        return false;
    }

}

?>