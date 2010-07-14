<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class FlickrExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'flickr';

    const PROPERTY_URLS = 'urls';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_TAGS = 'tags';

    const SIZE_SQUARE = 'square';
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_ORIGINAL = 'original';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS, self :: PROPERTY_LICENSE, self :: PROPERTY_TAGS));
    }

    static function get_default_sizes()
    {
        return array(self :: SIZE_SQUARE, self :: SIZE_THUMBNAIL, self :: SIZE_SMALL, self :: SIZE_MEDIUM, self :: SIZE_LARGE, self :: SIZE_ORIGINAL);
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

    function get_available_size_dimensions($size = self :: SIZE_MEDIUM)
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
        return array('width' => $urls[$size]['width'], 'height' => $urls[$size]['height']);
    }

    function get_available_size_dimensions_string($size = self :: SIZE_MEDIUM)
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

    function get_tags()
    {
        return $this->get_default_property(self :: PROPERTY_TAGS);
    }

    function set_tags($tags)
    {
        return $this->set_default_property(self :: PROPERTY_TAGS, $tags);
    }

    function get_tags_string()
    {
        $tags = array();
        foreach ($this->get_tags() as $tag)
        {
            $tags[] = '<a href="http://www.flickr.com/photos/tags/' . $tag['text'] . '">' . $tag['display'] . '</a>';
        }

        return implode(', ', $tags);
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

    function get_license_url()
    {
        $license = $this->get_license();
        return $license['url'];
    }

    function get_license_name()
    {
        $license = $this->get_license();
        return $license['name'];
    }

    function get_license_string()
    {
        $license_name = $this->get_license_name();
        $license_url = $this->get_license_url();

        if ($license_url)
        {
            return '<a href="' . $license_url . '">' . $license_name . '</a>';
        }
        else
        {
            return $license_name;
        }
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