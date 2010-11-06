<?php

namespace repository\content_object\glossary;

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
        $list = array('glossary' => 'glossary',
                'glossary_builder' => 'builder/glossary_builder',
                'glossary_complex_display_support' => 'display/glossary_complex_display_support',
                'glossary_complex_display_preview' => 'display/glossary_complex_display_preview',
                'glossary_display' => 'display/glossary_display');
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