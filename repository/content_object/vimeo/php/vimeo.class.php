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

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_URL, self :: PROPERTY_HEIGHT, self :: PROPERTY_WIDTH);
    }

    function get_video_url()
    {

        $video_url_custom = "http://vimeo.com/moogaloop.swf?clip_id=VIMEO_ID&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=ffffff&amp;fullscreen=1";
        $video_url_custom = str_replace("VIMEO_ID", $this->get_video_id(), $video_url_custom);

        //return 'http://vimeo.com/' . $this->get_video_id();
        return $video_url_custom;
    }

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }
}
?>