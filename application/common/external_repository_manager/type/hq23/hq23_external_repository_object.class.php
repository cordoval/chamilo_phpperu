<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class Hq23ExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'hq23';

    const PROPERTY_URLS = 'urls';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_ALBUM_NAME = 'album_name';
    const PROPERTY_TAGS = 'tags';

    const SIZE_SQUARE = 'square';
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_ORIGINAL = 'original';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TAGS, self :: PROPERTY_ALBUM_NAME));
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

    function get_tags_string($include_links = true)
    {
        $tags = array();
        foreach ($this->get_tags() as $tag)
        {
            if ($include_links)
            {
                $tags[] = '<a href="http://www.23developper.com/api/tag/list' . $tag['text'] . '">' . $tag['display'] . '</a>';
            }
            else
            {
                $tags[] = $tag['display'];
            }
        }

        return implode(', ', $tags);
    }
    
    function get_album_name()
    {
    	return $this->get_default_property(self :: PROPERTY_ALBUM_NAME);
    }
    
    function set_album_name($album_name)
    {
    	return $this->set_default_property(self :: PROPERTY_ALBUM_NAME, $album_name);
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
        return Theme :: get_common_image('external_repository/flickr/licenses/license_' . $this->get_license_id(), 'png', $this->get_license_name(), $this->get_license_url(), ToolbarItem :: DISPLAY_ICON);
    }

    function set_license($license)
    {
        return $this->set_default_property(self :: PROPERTY_LICENSE, $license);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    static function get_possible_sizes()
    {
        return array('sq' => self :: SIZE_SQUARE, 't' => self :: SIZE_THUMBNAIL, 's' => self :: SIZE_SMALL, 'm' => self :: SIZE_MEDIUM, 'l' => self :: SIZE_LARGE, 'o' => self :: SIZE_ORIGINAL);
    }

    static function get_possible_licenses()
    {
        $licenses = array();
        $licenses[0] = array('id' => 0, 'name' => 'All Rights Reserved', 'url' => '');
        $licenses[1] = array('id' => 1, 'name' => 'Attribution-NonCommercial-ShareAlike License', 'url' => 'http://creativecommons.org/licenses/by-nc-sa/2.0/');
        $licenses[2] = array('id' => 2, 'name' => 'Attribution-NonCommercial License', 'url' => 'http://creativecommons.org/licenses/by-nc/2.0/');
        $licenses[3] = array('id' => 3, 'name' => 'Attribution-NonCommercial-NoDerivs License', 'url' => 'http://creativecommons.org/licenses/by-nc-nd/2.0/');
        $licenses[4] = array('id' => 4, 'name' => 'Attribution License', 'url' => 'http://creativecommons.org/licenses/by/2.0/');
        $licenses[5] = array('id' => 5, 'name' => 'Attribution-ShareAlike License', 'url' => 'http://creativecommons.org/licenses/by-sa/2.0/');
        $licenses[6] = array('id' => 6, 'name' => 'Attribution-NoDerivs License', 'url' => 'http://creativecommons.org/licenses/by-nd/2.0/');
        $licenses[7] = array('id' => 7, 'name' => 'No known copyright restrictions', 'url' => 'http://www.flickr.com/commons/usage/');
        $licenses[8] = array('id' => 8, 'name' => 'United States Government Work', 'url' => 'http://www.usa.gov/copyright.shtml');
        return $licenses;
    }
}
?>