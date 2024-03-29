<?php

namespace repository\content_object\forum_topic;

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
        $list = array(
                'forum_topic' => 'forum_topic',
                'forum_topic_builder' => 'builder/forum_topic_builder',
                'forum_topic_complex_display_support' => 'display/forum_topic_complex_display_support',
                'forum_topic_complex_display_preview' => 'display/forum_topic_complex_display_preview',
                'forum_topic_display' => 'display/forum_topic_display');
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