<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class FlickrExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'flickr';

    const PROPERTY_URLS = 'urls';
    const PROPERTY_LICENSE = 'license';

    const SIZE_SQUARE = 'square';
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_ORIGINAL = 'original';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS, self :: PROPERTY_LICENSE));
    }

    static function get_default_sizes()
    {
        return array(self :: SIZE_SQUARE, self :: SIZE_THUMBNAIL, self :: SIZE_SMALL, self :: SIZE_MEDIUM, self :: SIZE_LARGE, self :: SIZE_ORIGINAL);
    }

    function get_available_sizes()
    {
        return array_keys($this->get_urls());
    }

    function get_urls()
    {
        return $this->get_default_property(self :: PROPERTY_URLS);
    }

    function set_urls($urls)
    {
        return $this->set_default_property(self :: PROPERTY_URLS, $urls);
    }

    function get_url($size = self :: SIZE_MEDIUM)
    {
        if (! in_array($size, self :: get_default_sizes()))
        {
            $size = self :: SIZE_MEDIUM;
        }

        if (! in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }

        $urls = $this->get_urls();
        return $urls[$size]['source'];
    }

    function get_license()
    {
        return $this->get_default_property(self :: PROPERTY_LICENSE);
    }

    function set_license($license)
    {
        return $this->set_default_property(self :: PROPERTY_LICENSE, $license);
    }

    function get_icon_name()
    {
        return self :: OBJECT_TYPE . '_' . parent :: get_icon_name();
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>