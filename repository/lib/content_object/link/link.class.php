<?php
/**
 * $Id: link.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */
class Link extends ContentObject
{
    const PROPERTY_URL = 'url';

    function get_url()
    {
        return $this->get_additional_property(self :: PROPERTY_URL);
    }

    function set_url($url)
    {
        return $this->set_additional_property(self :: PROPERTY_URL, $url);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_URL);
    }
}
?>