<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\Translation;

use common\extensions\external_repository_manager\StreamingMediaExternalRepositoryObjectDisplay;

require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/general/streaming/streaming_media_external_repository_object_display.class.php';

class YoutubeExternalRepositoryObjectDisplay extends StreamingMediaExternalRepositoryObjectDisplay
{

    function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()) . ')</h3>';
    }

    function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Status')] = $this->get_object()->get_status_text();
        $properties[Translation :: get('Category')] = Translation :: get($this->get_object()->get_category());
        $properties[Translation :: get('Tags')] = $this->get_object()->get_tags_string();
        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $html = array();
        $html[] = '<embed height="344" width="425" type="application/x-shockwave-flash" src="' . $object->get_video_url() . '"></embed>';
        return implode("\n", $html);
    }
}
?>