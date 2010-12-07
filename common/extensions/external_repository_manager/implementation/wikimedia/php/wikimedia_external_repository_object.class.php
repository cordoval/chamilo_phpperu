<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\extensions\external_repository_manager\ExternalRepositoryObject;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;

class WikimediaExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'wikimedia';

    const PROPERTY_URLS = 'urls';

    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM = 'medium';
    const SIZE_ORIGINAL = 'original';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS));
    }

    static function get_default_sizes()
    {
        return array(self :: SIZE_THUMBNAIL, self :: SIZE_MEDIUM, self :: SIZE_ORIGINAL);
    }

    function get_available_sizes()
    {
        return array_keys($this->get_urls());
    }

    function get_available_sizes_string()
    {
        $available_sizes = $this->get_available_sizes();
        $html = array();

        foreach ($available_sizes as $available_size)
        {
            $html[] = '<a href="' . $this->get_url($available_size) . '">' . Translation :: get(Utilities :: underscores_to_camelcase($available_size)) . ' (' . $this->get_available_size_dimensions_string($available_size) . ')</a>';
        }

        return implode('<br />' . "\n", $html);
    }

    function get_available_size_dimensions($size = self :: SIZE_THUMBNAIL)
    {
        if (! in_array($size, self :: get_default_sizes()))
        {
            $size = self :: SIZE_THUMBNAIL;
        }

        if (! in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }

        $urls = $this->get_urls();
        return array('width' => $urls[$size]['width'], 'height' => $urls[$size]['height']);
    }

    function get_available_size_dimensions_string($size = self :: SIZE_THUMBNAIL)
    {
        $available_size_dimensions = $this->get_available_size_dimensions($size);

        return $available_size_dimensions['width'] . ' x ' . $available_size_dimensions['height'];
    }

    function get_urls()
    {
        return $this->get_default_property(self :: PROPERTY_URLS);
    }

    function set_urls($urls)
    {
        return $this->set_default_property(self :: PROPERTY_URLS, $urls);
    }

    function get_url($size = self :: SIZE_THUMBNAIL)
    {
        if (! in_array($size, self :: get_default_sizes()))
        {
            $size = self :: SIZE_THUMBNAIL;
        }

        if (! in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }

        $urls = $this->get_urls();
        return $urls[$size]['source'];
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    static function get_possible_sizes()
    {
        return array('t' => self :: SIZE_THUMBNAIL, 'm' => self :: SIZE_MEDIUM, 'o' => self :: SIZE_ORIGINAL);
    }
}
?>