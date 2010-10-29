<?php

namespace repository\content_object\competence;

use common\libraries\Utilities;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array('competence' => 'competence.class.php',
            'competence_builder' => 'builder/competence_builder.class.php');
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