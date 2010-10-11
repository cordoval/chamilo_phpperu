<?php
/**
 * $Id: link.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */
class Link extends ContentObject implements Versionable
{
    const PROPERTY_URL = 'url';

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

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