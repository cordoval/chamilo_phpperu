<?php
namespace common\extensions\external_repository_manager\implementation\wikipedia;

use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

use common\libraries\Translation;

class WikipediaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent :: get_display_properties();

        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $url = $object->get_urls();
        $html = array();

        if ($is_thumbnail || !$url)
        {
            return parent :: get_preview($is_thumbnail);
        }
        else
        {
         	$html = array();
         	$html[] = '<iframe class="preview" src="' . $url. '"></iframe>';
          	return implode("\n", $html);
        }
    }
}
?>