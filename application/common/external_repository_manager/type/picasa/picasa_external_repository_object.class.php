<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class PicasaExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'picasa';
    
    const PROPERTY_URLS = 'urls';
    
    const SIZE_THUMBNAIL_SMALL = 'thumb_small';
    const SIZE_THUMBNAIL_MEDIUM = 'thumb_medium';
    const SIZE_THUMBNAIL_LARGE = 'thumb_large';
    const SIZE_ORIGINAL = 'original';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS));
    }

    static function get_default_sizes()
    {
        return array(self :: SIZE_THUMBNAIL_SMALL, self :: SIZE_THUMBNAIL_MEDIUM, self :: SIZE_THUMBNAIL_LARGE, self :: SIZE_ORIGINAL);
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

    function get_available_size_dimensions($size = self :: SIZE_THUMBNAIL_LARGE)
    {
        if (! in_array($size, self :: get_default_sizes()))
        {
            $size = self :: SIZE_THUMBNAIL_LARGE;
        }
        
        if (! in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }
        
        $urls = $this->get_urls();
        return array('width' => $urls[$size]['width'], 'height' => $urls[$size]['height']);
    }

    function get_available_size_dimensions_string($size = self :: SIZE_THUMBNAIL_LARGE)
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
    
    function get_url($size = self :: SIZE_THUMBNAIL_LARGE)
    {
        if (! in_array($size, self :: get_default_sizes()))
        {
            $size = self :: SIZE_THUMBNAIL_LARGE;
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

}
?>