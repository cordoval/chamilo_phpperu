<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

use common\libraries\Translation;

class SoundcloudExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    //    function get_display_properties()
    //    {
    //        $object = $this->get_object();
    //
    //        $properties = parent :: get_display_properties();
    //        $properties[Translation :: get('AvailableSizes')] = $object->get_available_sizes_string();
    //        $properties[Translation :: get('Tags')] = $object->get_tags_string();
    //        $properties[Translation :: get('License')] = $object->get_license_icon();
    //
    //        return $properties;
    //    }


    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();

        if ($is_thumbnail && $object->get_artwork())
        {
            $class = ($is_thumbnail ? 'thumbnail' : 'with_border');

            $html = array();
            $html[] = '<img class="' . $class . '" src="' . $object->get_artwork() . '" />';
            return implode("\n", $html);
        }
        elseif(!$is_thumbnail)
        {
            $preview_url = urlencode('http://api.soundcloud.com/tracks/' . $object->get_id());

            return '<object height="81" width="100%">
            <param name="movie" value="http://player.soundcloud.com/player.swf?url='. $preview_url .'&secret_url=false"></param>
            <param name="allowscriptaccess" value="always"></param>
            <embed allowscriptaccess="always" height="81" src="http://player.soundcloud.com/player.swf?url='. $preview_url .'&secret_url=false" type="application/x-shockwave-flash" width="100%"></embed>
            </object>';
            //<span><a href="http://soundcloud.com/r-man/the-reprise">THE REPRISE</a> by <a href="http://soundcloud.com/r-man">[thelemonchill]</a></span>';
        }
        else
        {
            return parent :: get_preview($is_thumbnail);
        }
    }
}
?>