<?php
/**
 * $Id: photobucket.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
class Photobucket extends ContentObject implements Versionable
{
    const PROPERTY_PHOTOBUCKET_ID = 'photobucket_id';
    const PROPERTY_HEIGHT = 'height';
    const PROPERTY_WIDTH = 'width';
    const PROPERTY_THUMBNAIL = 'thumbnail';
    const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_photobucket_id()
    {
        return $this->get_additional_property(self :: PROPERTY_PHOTOBUCKET_ID);
    }

    function set_photobucket($photobucket)
    {
        return $this->set_additional_property(self :: PROPERTY_PHOTOBUCKET_ID, $photobucket);
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

	function get_thumbnail()
    {
        return $this->get_additional_property(self :: PROPERTY_THUMBNAIL);
    }

    function set_thumbnail($thumbnail)
    {
        return $this->set_additional_property(self :: PROPERTY_THUMBNAIL, $thumbnail);
    }
    	
    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_PHOTOBUCKET_ID, self :: PROPERTY_THUMBNAIL);
    }

//    function get_video_id()
//    {
//        $video_url = $this->get_url();
//        $video_url_components = parse_url($video_url);
//        $video_query_components = Text :: parse_query_string($video_url_components['query']);
//
//        return $video_query_components['current'];
//    }
//
//    function get_video_url()
//    {
//        return 'http://s799.photobucket.com/albums/yy278/MagaliGillard/' . $this->get_album() . '/?action=view&current=' . $this->get_video_id();
//    }

    
    function get_video_url()
    {
        $conditions = array();
    	$conditions[] = new EqualityCondition(ExternalRepositorySetting::PROPERTY_VARIABLE, 'url');
      	$conditions[] = new EqualityCondition(ExternalRepositorySetting::PROPERTY_EXTERNAL_REPOSITORY_ID, $this->get_synchronization_data()->get_external_repository_id());
        $condition = new AndCondition($conditions);
        $settings = $this->get_data_manager()->retrieve_external_repository_settings($condition);
    	if ($settings->size() == 1)
    	{
    		$settings = $settings->next_result();
    		$url = $settings->get_value();
    	}
    	return $url . '/albums/yy278/MagaliGillard/titi/?action=view&current=' . $this->get_photobucket_id();
    }
}
?>