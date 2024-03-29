<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;

use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\Translation;

use repository\ExternalSetting;

use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;
use common\extensions\external_repository_manager\StreamingMediaExternalRepositoryObjectDisplay;

require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/general/streaming/streaming_media_external_repository_object_display.class.php';

/**
 *
 * @author magali.gillard
 *
 */
class MatterhornExternalRepositoryObjectDisplay extends StreamingMediaExternalRepositoryObjectDisplay
{
    function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Series')] = $this->get_object()->get_series();
        $properties[Translation :: get('Contributor')] = $this->get_object()->get_contributors();
        $properties[Translation :: get('Subject')] = $this->get_object()->get_subjects();
        $properties[Translation :: get('License')] = $this->get_object()->get_license();
        $number = 1;
        foreach($this->get_object()->get_tracks() as $track)
        {
        	$properties[Translation :: get('Track') . ' ' . $number] = $track->as_string();
        	$number ++;
        }

        $number = 1;
    	foreach($this->get_object()->get_attachments() as $attachment)
        {
        	$properties[Translation :: get('Attachment') . ' ' . $number] = $attachment->as_string();
        	$number ++;
        }
        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
    	$object = $this->get_object();
    	$settings = ExternalSetting :: get('url', $object->get_external_repository_id());
		
        $html = array();

		if ($is_thumbnail)
		{
        	$width = 320;
        	$height = 356;
        	$html[] = '<img class="thumbnail" src="' . $object->get_search_preview()->get_url() . '"/>';
		}
		else
		{
			$width = 620;
        	$height = 596;
        	$html[] = '<iframe src="' . $settings . '/engage/ui/embed.html?id=' . $object->get_id(). '" style="border:0px #FFFFFF none;" name="Opencast Matterhorn - Media Player" scrolling="no" frameborder="1" marginheight="0px" marginwidth="0px" width="'. $width . '" height="'. $height .'"></iframe>';
		}

		return implode("\n", $html);
    }

 	function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()/1000) . ')</h3>';
    }

}
?>