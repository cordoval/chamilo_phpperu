<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

abstract class StreamingMediaExternalRepositoryObject extends ExternalRepositoryObject
{

    const PROPERTY_URL = 'url';
    const PROPERTY_DURATION = 'duration';
    const PROPERTY_THUMBNAIL = 'thumbnail';
    const PROPERTY_STATUS = 'status';
    const STATUS_AVAILABLE = 1;
    const STATUS_UNAVAILABLE = 2;

    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_URL;
        $extended_property_names[] = self :: PROPERTY_DURATION;
        $extended_property_names[] = self :: PROPERTY_THUMBNAIL;
        $extended_property_names[] = self :: PROPERTY_STATUS;
        return parent :: get_default_property_names($extended_property_names);
    }

    public function get_status_text()
    {
        switch ($this->get_status())
        {
            case self :: STATUS_AVAILABLE :
                return Translation :: get('Available');
                break;
            case self :: STATUS_UNAVAILABLE :
                return Translation :: get('Unavailable');
                break;
            default :
                return Translation :: get('Unknown');
        }
    }

    function is_usable()
    {
        if ($this->get_status() == self :: STATUS_AVAILABLE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return the $title
     */
    /**
     * @return the $thumbnail
     */
    public function get_thumbnail()
    {
        return $this->get_default_property(self :: PROPERTY_THUMBNAIL);
    }

    /**
     * @param $thumbnail the $thumbnail to set
     */
    public function set_thumbnail($thumbnail)
    {
        $this->set_default_property(self :: PROPERTY_THUMBNAIL, $thumbnail);
    }

    /**
     * @return the $url
     */
    public function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    /**
     * @return the $duration
     */
    public function get_duration()
    {
        return $this->get_default_property(self :: PROPERTY_DURATION);
    }

    public function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    public function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * @param $duration the $duration to set
     */
    public function set_duration($duration)
    {
        $this->set_default_property(self :: PROPERTY_DURATION, $duration);
    }
}
?>