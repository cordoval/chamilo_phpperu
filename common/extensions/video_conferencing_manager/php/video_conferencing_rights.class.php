<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;

abstract class VideoConferencingRights
{
    private $moderator;

    function get_moderator()
    {
        return $this->moderator;
    }

    function set_moderator($is_moderator)
    {
        $this->moderator = $is_moderator;
    }

    function is_moderator()
    {
        return $this->get_moderator();
    }

    static function factory($type)
    {
        $namespace = VideoConferencingManager :: get_namespace($type);
        $file = Path :: namespace_to_path($namespace) . '/' . Path :: CLASS_PATH . '/' . $type . '_video_conferencing_rights.class.php';
        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('VideoConferencingRightsFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';

            Display :: header();
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }
        else
        {
            require_once $file;
            $class = $namespace . '\\' . Utilities :: underscores_to_camelcase($type) . 'VideoConferencingRights';

            return new $class();
        }

    }
}
?>