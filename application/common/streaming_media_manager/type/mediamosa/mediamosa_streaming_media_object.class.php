<?php
/**
 * Description of mediamosa_streaming_video_objectclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../../streaming_media_object.class.php';

class MediamosaStreamingMediaObject extends StreamingMediaObject {

    private $mediafiles;

    const PROPERTY_CONVERSION_STATE = 'conversion_state';
    const PROPERTY_DATE_PUBLISHED = 'date'; //date of publishing
    const PROPERTY_DATE_CREATED = 'creation_date'; //date of creation
    const PROPERTY_PUBLISHER = 'publisher';
    const PROPERTY_CREATOR = 'creator';
    const PROPERTY_DEFAULT_MEDIAFILE = 'default_mediafile';

    const STATE_PUBLIC = 0;
    const STATE_QUEUED = 1;
    const STATE_TRANSCODING = 2;
    const STATE_ERRONEOUS = 3;

    function get_type()
    {
        return 'mediamosa';
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_CATEGORY, self :: PROPERTY_TAGS);
    }

    function set_conversion_state($conversion_state)
    {
        $this->set_additional_property(self :: PROPERTY_CONVERSION_STATE, $conversion_state);
    }

    function get_conversion_state()
    {
        return $this->get_additional_property(self :: PROPERTY_CONVERSION_STATE);
    }

    function set_date($date)
    {
        $this->set_additional_property(self :: PROPERTY_DATE_PUBLISHED, $date);
    }

    function get_date()
    {
        return $this->get_additional_property(self :: PROPERTY_DATE_PUBLISHED);
    }

    function set_publisher($publisher)
    {
        $this->set_additional_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    function get_publisher()
    {
        return $this->get_additional_property(self::PROPERTY_PUBLISHER);
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
        $this->set_default_property(self ::PROPERTY_DEFAULT_MEDIAFILE,$default_mediafile);
    }

    function get_default_mediafile()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_MEDIAFILE);
    }

}
?>
