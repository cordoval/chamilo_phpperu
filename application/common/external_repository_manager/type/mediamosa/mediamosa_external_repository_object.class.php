<?php
/**
 * Description of mediamosa_streaming_video_objectclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class MediamosaExternalRepositoryObject extends ExternalRepositoryObject
{
    
    private $mediafiles;
    
    const OBJECT_TYPE = 'mediamosa';
    
    const PROPERTY_CONVERSION_STATE = 'conversion_state';
    const PROPERTY_DATE_PUBLISHED = 'date'; //date of publishing
    const PROPERTY_DATE_CREATED = 'creation_date'; //date of creation
    const PROPERTY_PUBLISHER = 'publisher';
    const PROPERTY_CREATOR = 'creator';
    const PROPERTY_DEFAULT_MEDIAFILE = 'default_mediafile';
    const PROPERTY_IS_DOWNLOADABLE = 'is_downloadable';
    const PROPERTY_THUMBNAIL = 'thumbnail';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_DURATION = 'duration';
    const PROPERTY_OWNER_ID = 'owner_id';
    
    const STATUS_UNAVAILABLE = 'unavailable';
    const STATUS_AVAILABLE = 'available';
    const STATE_PUBLIC = 0;
    const STATE_QUEUED = 1;
    const STATE_TRANSCODING = 2;
    const STATE_ERRONEOUS = 3;

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_OWNER_ID, self :: PROPERTY_CONVERSION_STATE, self :: PROPERTY_DATE_PUBLISHED, self :: PROPERTY_DATE_CREATED, self :: PROPERTY_PUBLISHER, self :: PROPERTY_CREATOR, self :: PROPERTY_DEFAULT_MEDIAFILE, self :: PROPERTY_IS_DOWNLOADABLE, self :: PROPERTY_STATUS, self :: PROPERT_THUMBNAIL, self :: PROPERTY_DURATION));
    }
    
    function set_conversion_state($conversion_state)
    {
        $this->set_default_property(self :: PROPERTY_CONVERSION_STATE, $conversion_state);
    }

    function get_conversion_state()
    {
        return $this->get_default_property(self :: PROPERTY_CONVERSION_STATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE_PUBLISHED, $date);
    }

    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_PUBLISHED);
    }

    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    function set_creator($creator)
    {
        $this->set_default_property(self :: PROPERTY_CREATOR, $creator);
    }

    function get_creator()
    {
        return $this->get_default_property(self :: PROPERTY_CREATOR);
    }

    /*
     * adds mediafile object to array mediafiles member
     * @param mediafile object
     */
    function add_mediafile($mediafile)
    {
        $this->mediafiles[$mediafile->get_id()] = $mediafile;
    }

    function get_mediafiles()
    {
        return $this->mediafiles;
    }

    function get_mediafile($mediafile_id)
    {
        return isset($this->mediafiles[$mediafile_id]) ? $this->mediafiles[$mediafile_id] : false;
    }

    function set_default_mediafile($default_mediafile)
    {
        $this->set_default_property(self :: PROPERTY_DEFAULT_MEDIAFILE, $default_mediafile);
    }

    function get_default_mediafile()
    {
        
        if (! $this->get_default_property(self :: PROPERTY_DEFAULT_MEDIAFILE))
        {
            if (is_array($this->mediafiles))
            {
                $keys = array_keys($this->mediafiles);
                return array_pop($keys);
            }
            return false;
        }
        return $this->get_default_property(self :: PROPERTY_DEFAULT_MEDIAFILE);
    }

    function set_is_downloadable($is_downloadable)
    {
        $this->set_default_property(self :: PROPERTY_IS_DOWNLOADABLE, $is_downloadable);
    }

    function get_is_downloadable()
    {
        return $this->get_default_property(self :: PROPERTY_IS_DOWNLOADABLE);
    }

    function set_thumbnail($thumbnail)
    {
        $this->set_default_property(self :: PROPERTY_THUMBNAIL, $thumbnail);
    }

    function get_thumbnail()
    {
        return $this->get_default_property(self :: PROPERTY_THUMBNAIL);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_duration($duration)
    {
        $this->set_default_property(self :: PROPERTY_DURATION, $duration);
    }

    function get_duration()
    {
        return $this->get_default_property(self :: PROPERTY_DURATION);
    }

    function set_owner_id($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

    function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>
