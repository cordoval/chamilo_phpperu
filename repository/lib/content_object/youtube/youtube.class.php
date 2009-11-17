<?php
/**
 * $Id: youtube.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
class Youtube extends ContentObject
{
    const PROPERTY_URL = 'url';
    const PROPERTY_HEIGHT = 'height';
    const PROPERTY_WIDTH = 'width';

    function get_url()
    {
        return $this->get_additional_property(self :: PROPERTY_URL);
    }

    function set_url($url)
    {
        return $this->set_additional_property(self :: PROPERTY_URL, $url);
    }

    function get_height()
    {
        return $this->get_additional_property(self :: PROPERTY_HEIGHT);
    }

    function set_height($height)
    {
        return $this->set_additional_property(self :: PROPERTY_HEIGHT, $height);
    }

    function get_width()
    {
        return $this->get_additional_property(self :: PROPERTY_WIDTH);
    }

    function set_width($width)
    {
        return $this->set_additional_property(self :: PROPERTY_WIDTH, $width);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_URL, self :: PROPERTY_HEIGHT, self :: PROPERTY_WIDTH);
    }
}
?>