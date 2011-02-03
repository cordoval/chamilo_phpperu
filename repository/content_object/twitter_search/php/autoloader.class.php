<?php

namespace repository\content_object\twitter_search;

use common\libraries\Utilities;

/**
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array('twitter_search' => 'twitter_search');
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