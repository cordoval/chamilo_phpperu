<?php
/**
 * $Id: youtube.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
class Youtube extends ContentObject implements Versionable
{
    const PROPERTY_URL = 'url';
    const PROPERTY_HEIGHT = 'height';
    const PROPERTY_WIDTH = 'width';
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

    function get_video_id()
    {
        $video_url = $this->get_url();
        $video_url_components = parse_url($video_url);
        $video_query_components = Text :: parse_query_string($video_url_components['query']);

        return $video_query_components['v'];
    }

    function get_video_url()
    {
        return 'http://www.youtube.com/v/' . $this->get_video_id();
    }
}
?>