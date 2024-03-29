<?php
namespace repository\content_object\blog;

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
        $list = array('blog' => 'blog',
            'blog_builder' => 'builder/blog_builder',
            'blog_complex_display_support' => 'display/blog_complex_display_support',
            'blog_complex_display_preview' => 'display/blog_complex_display_preview',
            'blog_display' => 'display/blog_display',
            'blog_layout' => 'display/component/viewer/blog_layout'
        );
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