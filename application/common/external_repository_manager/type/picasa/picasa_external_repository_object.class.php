<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class PicasaExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'picasa';

    const PROPERTY_URLS = 'urls';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_OWNER = 'owner';
    const PROPERTY_TAGS = 'tags';
    const PROPERTY_ALBUM = 'album';

    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM = 'medium';
    const SIZE_ORIGINAL = 'original';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS, self :: PROPERTY_LICENSE, self :: PROPERTY_OWNER, self :: PROPERTY_TAGS, self :: PROPERTY_ALBUM));
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

    function get_license_id()
    {
        $license = $this->get_license();
        return $license['id'];
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
        if ($this->get_license_url())
        {
            return '<a href="' . $this->get_license_url() . '">' . $this->get_license_name() . '</a>';
        }
        else
        {
            return $this->get_license_name();
        }
    }

    function get_license_icon()
    {
        return Theme :: get_common_image('external_repository/picasa/licenses/license_' . $this->get_license_id(), 'png', $this->get_license_name(), $this->get_license_url(), ToolbarItem :: DISPLAY_ICON);
    }

    function set_license($license)
    {
        return $this->set_default_property(self :: PROPERTY_LICENSE, $license);
    }

    function get_owner()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER);
    }

    function set_owner($owner)
    {
        return $this->set_default_property(self :: PROPERTY_OWNER, $owner);
    }

    function get_owner_string()
    {
        $string = ($this->get_owner() ? $this->get_owner() . ' (' : '');
        $string .= $this->get_owner_id();
        $string .= ($this->get_owner() ? ')' : '');
        return $string;
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
        return implode(', ', $this->get_tags());
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

}
?>