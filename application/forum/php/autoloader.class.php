<?php

namespace application\forum;

use common\libraries\WebApplication;
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
        
        $list = array(
            'forum_rights' => 'forum_rights.class.php',
            'forum_data_manager' => 'forum_data_manager.class.php',
            'forum_data_manager_interface' => 'forum_data_manager_interface.class.php',
            'forum_publication' => 'forum_publication.class.php',
            'forum_manager' => 'forum_manager/forum_manager.class.php',
            'forum_publication_form' => 'forms/forum_publication_form.class.php',
            'forum_publication_publisher' => 'publisher/forum_publication_publisher.class.php',
            'forum_publication_category_manager' => 'category_manager/forum_publication_category_manager.class.php',
            'forum_publication_category' => 'category_manager/forum_publication_category.class.php',
			'forum_topic_view_tracker' => '../trackers/forum_topic_view_tracker.class.php'        
        );

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('forum') . $url;
            return true;
        }

        return false;
    }

}