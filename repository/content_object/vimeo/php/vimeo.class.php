<?php
namespace repository\content_object\vimeo;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\Text;

use repository\ContentObject;

/**
 * $Id: vimeo.class.php 2010-06-08
 * package repository.lib.content_object.vimeo
 * @author Shoira Mukhsinova
 */
class Vimeo extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;
    
    const VIMEO_PLAYER_URI = 'http://vimeo.com/moogaloop.swf?clip_id=%s&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=ffffff&amp;fullscreen=1"';

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_video_url()
    {
        $video_url_custom = sprintf(self :: VIMEO_PLAYER_URI, $this->get_synchronization_data()->get_external_repository_object_id());

        return $video_url_custom;
    }  
}
?>