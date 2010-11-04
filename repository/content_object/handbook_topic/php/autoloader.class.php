<?php

namespace repository\content_object\handbook_topic;

use common\libraries\Utilities;


class Autoloader
{

    static function load($classname)
    {
        $list = array('handbook_topic' => 'handbook_topic.class.php');
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url;
            return true;
        }

        return false;
    }

}

?>