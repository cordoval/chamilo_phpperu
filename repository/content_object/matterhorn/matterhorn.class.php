<?php
class Matterhorn extends ContentObject implements Versionable
{
    const PROPERTY_MATTERHORN_ID = 'matterhorn_id';
    const PROPERTY_HEIGHT = 'height';
    const PROPERTY_WIDTH = 'width';
    const PROPERTY_THUMBNAIL = 'thumbnail';
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_matterhorn_id()
    {
        return $this->get_additional_property(self :: PROPERTY_MATTERHORN_ID);
    }

    function set_matterhorn_id($matterhorn_id)
    {
        return $this->set_additional_property(self :: PROPERTY_MATTERHORN_ID, $matterhorn_id);
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
        return array(self :: PROPERTY_MATTERHORN_ID, self :: PROPERTY_THUMBNAIL/*, self :: PROPERTY_HEIGHT, self :: PROPERTY_WIDTH*/);
    }

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
    	return $url . '/engage/ui/embed.html?id=' . $this->get_matterhorn_id();
    }
}
?>